<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAdvisorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_ai_filtered_products(): void
    {
        config()->set('services.gemini.api_key', 'test-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => '{"price_max":400,"use_case":"gaming","performance":"high","condition":"used"}',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'description' => 'Samsung devices',
        ]);

        $matchingProduct = Product::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'brand' => 'Samsung',
            'model' => 'Galaxy S23',
            'name' => 'Samsung Galaxy S23 Gaming Edition',
            'slug' => 'samsung-galaxy-s23-gaming-edition',
            'description' => 'Great gaming phone with Snapdragon and 12GB RAM.',
            'price' => 399.99,
            'condition' => 'used',
            'status' => 'available',
        ]);

        Product::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'brand' => 'Samsung',
            'model' => 'Galaxy A15',
            'name' => 'Samsung Galaxy A15',
            'slug' => 'samsung-galaxy-a15',
            'description' => 'Budget device for general use.',
            'price' => 450,
            'condition' => 'used',
            'status' => 'available',
        ]);

        $response = $this->postJson('/api/v1/ai/advisor', [
            'query' => 'بدي موبايل للألعاب بسعر 400',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'AI recommendations generated successfully.')
            ->assertJsonPath('data.filters.price_max', 400)
            ->assertJsonPath('data.filters.use_case', 'gaming')
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.id', $matchingProduct->id);
    }

    public function test_it_returns_clean_error_when_ai_response_is_invalid(): void
    {
        config()->set('services.gemini.api_key', 'test-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'this is not json',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/ai/advisor', [
            'query' => 'بدي موبايل جيد',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'AI_INVALID_JSON')
            ->assertJsonPath('message', 'AI response did not contain a JSON object.');
    }

    public function test_it_rejects_non_phone_queries_as_out_of_scope(): void
    {
        config()->set('services.gemini.api_key', 'test-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => '{"error":"OUT_OF_SCOPE"}',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/ai/advisor', [
            'query' => 'مين هو ريال مدريد؟',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'AI_OUT_OF_SCOPE')
            ->assertJsonPath('message', 'The request is خارج نطاق البحث عن الهواتف.');
    }

    public function test_it_rejects_mixed_queries_as_out_of_scope(): void
    {
        config()->set('services.gemini.api_key', 'test-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => '{"error":"OUT_OF_SCOPE"}',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/ai/advisor', [
            'query' => 'بدي موبايل واحكيلي نكتة',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'AI_OUT_OF_SCOPE')
            ->assertJsonPath('message', 'The request is خارج نطاق البحث عن الهواتف.');
    }
}
