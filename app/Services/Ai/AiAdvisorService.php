<?php

namespace App\Services\Ai;

use App\Exceptions\AiAdvisorException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AiAdvisorService
{
    private const OUT_OF_SCOPE_ERROR = 'OUT_OF_SCOPE';

    private const ALLOWED_FILTER_KEYS = [
        'price_max',
        'price_min',
        'condition',
        'brand',
        'performance',
        'use_case',
    ];

    public function advise(string $query): array
    {
        $filters = $this->generateFilters($query);
        $products = $this->findProducts($filters);

        return [
            'filters' => $filters,
            'products' => $products,
        ];
    }

    public function generateFilters(string $query): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $endpoint = (string) config('services.gemini.endpoint');

        if ($apiKey === '') {
            throw new AiAdvisorException(
                'AI advisor is not configured.',
                'AI_CONFIGURATION_ERROR',
                500,
            );
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $this->buildPrompt($query),
                                ],
                            ],
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new AiAdvisorException(
                'Unable to reach the AI provider.',
                'AI_PROVIDER_UNAVAILABLE',
                503,
            );
        }

        if ($response->failed()) {
            throw new AiAdvisorException(
                'Failed to generate AI recommendations.',
                'AI_PROVIDER_ERROR',
                502,
                [
                    'provider_status' => $response->status(),
                    'provider_response' => Arr::only($response->json() ?? [], ['error']),
                ],
            );
        }

        $rawText = $this->extractResponseText($response->json());
        $decoded = $this->decodeFilters($rawText);

        return $this->validateFilters($decoded);
    }

    public function findProducts(array $filters): Collection
    {
        $query = Product::query()
            ->with(['seller', 'category', 'images', 'variants'])
            ->where('status', 'available');

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['brand'])) {
            $brand = $filters['brand'];

            $query->where(function (Builder $builder) use ($brand) {
                $builder
                    ->where('brand', 'like', '%'.$brand.'%')
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($brand) {
                        $categoryQuery
                            ->where('name', 'like', '%'.$brand.'%')
                            ->orWhere('slug', 'like', '%'.$brand.'%');
                    });
            });
        }

        $this->applyHeuristicFilters($query, $filters);

        return $query->latest()->limit(20)->get();
    }

    private function buildPrompt(string $query): string
    {
        return <<<PROMPT
You are an AI assistant specialized ONLY in mobile phone marketplace queries.

Your task is to convert user input into structured JSON filters for phone products.

STRICT RULES:

* Only process queries related to mobile phones.
* If the query is NOT related to phones, return EXACTLY:
{
  "error": "OUT_OF_SCOPE"
}
* If the query mixes phone shopping with any unrelated request, return EXACTLY:
{
  "error": "OUT_OF_SCOPE"
}
* Do NOT answer general questions
* Do NOT explain anything
* ONLY return valid JSON

Allowed fields:

* price_max
* price_min
* condition
* brand
* performance
* use_case

User input:
{$query}
PROMPT;
    }

    private function extractResponseText(array $payload): string
    {
        $text = data_get($payload, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new AiAdvisorException(
                'AI returned an empty response.',
                'AI_INVALID_RESPONSE',
                502,
            );
        }

        return trim($text);
    }

    private function decodeFilters(string $rawText): array
    {
        $json = $this->extractJsonPayload($rawText);
        $decoded = json_decode($json, true);

        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new AiAdvisorException(
                'AI returned invalid JSON filters.',
                'AI_INVALID_JSON',
                422,
                [
                    'raw_response' => $rawText,
                ],
            );
        }

        $this->ensureRequestIsInScope($decoded);

        return Arr::only($decoded, self::ALLOWED_FILTER_KEYS);
    }

    private function ensureRequestIsInScope(array $decoded): void
    {
        if (($decoded['error'] ?? null) !== self::OUT_OF_SCOPE_ERROR) {
            return;
        }

        throw new AiAdvisorException(
            'The request is خارج نطاق البحث عن الهواتف.',
            'AI_OUT_OF_SCOPE',
            422,
        );
    }

    private function extractJsonPayload(string $rawText): string
    {
        $trimmed = trim($rawText);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');

        if ($start === false || $end === false || $end < $start) {
            throw new AiAdvisorException(
                'AI response did not contain a JSON object.',
                'AI_INVALID_JSON',
                422,
                [
                    'raw_response' => $rawText,
                ],
            );
        }

        return substr($trimmed, $start, $end - $start + 1);
    }

    private function validateFilters(array $filters): array
    {
        $normalized = array_filter([
            'price_max' => $this->normalizeNumericValue($filters['price_max'] ?? null),
            'price_min' => $this->normalizeNumericValue($filters['price_min'] ?? null),
            'condition' => $this->normalizeEnumValue($filters['condition'] ?? null),
            'brand' => $this->normalizeStringValue($filters['brand'] ?? null),
            'performance' => $this->normalizeEnumValue($filters['performance'] ?? null),
            'use_case' => $this->normalizeEnumValue($filters['use_case'] ?? null),
        ], static fn ($value) => ! is_null($value) && $value !== '');

        $validator = Validator::make($normalized, [
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['nullable', 'in:new,used'],
            'brand' => ['nullable', 'string', 'max:255'],
            'performance' => ['nullable', 'in:low,medium,high'],
            'use_case' => ['nullable', 'in:gaming,camera,battery,general'],
        ]);

        $validator->after(function ($validator) use ($normalized) {
            if (
                isset($normalized['price_min'], $normalized['price_max'])
                && $normalized['price_min'] > $normalized['price_max']
            ) {
                $validator->errors()->add('price_min', 'The minimum price cannot exceed the maximum price.');
            }
        });

        if ($validator->fails()) {
            throw new AiAdvisorException(
                'AI returned unsupported filters.',
                'AI_INVALID_FILTERS',
                422,
                $validator->errors()->toArray(),
            );
        }

        return $validator->validated();
    }

    private function normalizeNumericValue(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $sanitized = preg_replace('/[^\d.]/', '', $value);

        return is_numeric($sanitized) ? (float) $sanitized : null;
    }

    private function normalizeEnumValue(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? strtolower(trim($value)) : null;
    }

    private function normalizeStringValue(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function applyHeuristicFilters(Builder $query, array $filters): void
    {
        $useCase = $filters['use_case'] ?? null;
        $performance = $filters['performance'] ?? null;

        if ($useCase === 'gaming' || $performance === 'high') {
            $this->applyKeywordMatch($query, [
                'gaming',
                'game',
                'snapdragon',
                '8gb',
                '12gb',
                '16gb',
                'flagship',
                'performance',
            ]);
        }

        if ($useCase === 'camera') {
            $this->applyKeywordMatch($query, [
                'camera',
                'كاميرا',
                'photography',
                'pixel',
                'iphone',
                'pro',
            ]);
        }

        if ($useCase === 'battery') {
            $this->applyKeywordMatch($query, [
                'battery',
                'بطارية',
                '5000mah',
                '6000mah',
                'long-lasting',
            ]);
        }
    }

    private function applyKeywordMatch(Builder $query, array $keywords): void
    {
        $query->where(function (Builder $builder) use ($keywords) {
            foreach ($keywords as $keyword) {
                $builder
                    ->orWhere('name', 'like', '%'.$keyword.'%')
                    ->orWhere('brand', 'like', '%'.$keyword.'%')
                    ->orWhere('model', 'like', '%'.$keyword.'%')
                    ->orWhere('description', 'like', '%'.$keyword.'%')
                    ->orWhere('condition_notes', 'like', '%'.$keyword.'%')
                    ->orWhere('accessories', 'like', '%'.$keyword.'%');
            }
        });
    }
}
