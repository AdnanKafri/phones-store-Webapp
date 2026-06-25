# AI Search API Docs

This document describes the AI-powered phone advisor endpoint used by the web platform and Flutter app.

## Endpoint

`POST /api/v1/ai/advisor`

## Purpose

Accepts a natural-language phone shopping request in Arabic or English, converts it into structured filters, then returns matching products.

Examples:

- `بدي موبايل قوي للألعاب بسعر لا يتجاوز 450$`
- `I need a used iPhone with strong camera`
- `هاتف بطارية قوية للاستخدام اليومي`

## Request

### Headers

`Accept: application/json`

`Content-Type: application/json`

### Body

```json
{
  "query": "بدي موبايل قوي للألعاب بسعر لا يتجاوز 450$"
}
```

### Validation

- `query`: required, string, min `2`, max `1000`

## Success Response

### HTTP 200

```json
{
  "data": {
    "filters": {
      "price_max": 450,
      "performance": "high",
      "use_case": "gaming"
    },
    "products": [
      {
        "id": 31,
        "name": "iPhone 13",
        "slug": "iphone-13-31",
        "brand": "Apple",
        "model": "iPhone 13",
        "description": "Excellent condition",
        "defects": null,
        "condition_notes": null,
        "accessories": null,
        "disassembled_is": false,
        "reason_disassembly": null,
        "price": 430,
        "condition": "used",
        "status": "available",
        "source": "user",
        "color": "Blue",
        "location": "Damascus",
        "primary_image_url": "http://localhost/storage/products/example.jpg",
        "created_at": "2026-06-25T12:00:00.000000Z",
        "updated_at": "2026-06-25T12:00:00.000000Z",
        "seller": {
          "id": 4,
          "name": "Ahmad",
          "username": "ahmad",
          "location": "Damascus"
        },
        "category": {
          "id": 2,
          "name": "Apple",
          "slug": "apple"
        },
        "images": [
          {
            "id": 10,
            "url": "http://localhost/storage/products/example.jpg",
            "is_primary": true
          }
        ],
        "variants": []
      }
    ],
    "search_meta": {
      "fallback_applied": false,
      "match_strategy": "strict",
      "result_count": 1,
      "applied_filters": {
        "price_max": 450,
        "performance": "high",
        "use_case": "gaming"
      },
      "relaxed_filters": []
    }
  },
  "message": "AI recommendations generated successfully."
}
```

## Response Fields

### `filters`

AI-interpreted structured filters.

Supported keys:

- `price_max`
- `price_min`
- `condition`
- `brand`
- `performance`
- `use_case`

### `products`

Array of matching products using the standard `ProductResource` shape.

### `search_meta`

Optional metadata about how the backend matched products.

Fields:

- `fallback_applied`: boolean
- `match_strategy`: string
- `result_count`: integer
- `applied_filters`: object
- `relaxed_filters`: array of filter names that were relaxed

Possible `match_strategy` values:

- `strict`
- `without_preferences`
- `relaxed_budget`
- `flexible_condition`
- `brand_or_budget`
- `*_no_results` variants when no products are found after fallback attempts

## Empty Results

The endpoint still returns `200 OK` when the request is valid but no products match.

Example:

```json
{
  "data": {
    "filters": {
      "brand": "Apple",
      "price_max": 120
    },
    "products": [],
    "search_meta": {
      "fallback_applied": true,
      "match_strategy": "brand_or_budget_no_results",
      "result_count": 0,
      "applied_filters": {
        "brand": "Apple",
        "price_max": 138
      },
      "relaxed_filters": [
        "price_max"
      ]
    }
  },
  "message": "AI recommendations generated successfully."
}
```

## Error Cases

### OUT_OF_SCOPE

Returned when the request is not about shopping for phones.

### HTTP 422

```json
{
  "message": "The request is خارج نطاق البحث عن الهواتف.",
  "code": "AI_OUT_OF_SCOPE"
}
```

Examples that should trigger this:

- `احسبلي 2 + 2`
- `Write me a poem`
- `أريد هاتفاً ثم احجز لي فندقاً`

### Validation Error

### HTTP 422

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "query": [
      "The query field is required."
    ]
  }
}
```

### AI Provider Unavailable

### HTTP 503

```json
{
  "message": "Unable to reach the AI provider.",
  "code": "AI_PROVIDER_UNAVAILABLE"
}
```

### AI Provider Error

### HTTP 502

```json
{
  "message": "Failed to generate AI recommendations.",
  "code": "AI_PROVIDER_ERROR",
  "errors": {
    "provider_status": 500,
    "provider_response": {
      "error": {}
    }
  }
}
```

## Flutter Integration Notes

- Send the user text exactly as entered.
- Read product cards from `data.products`.
- Use `data.filters` to show “interpreted search filters” in the UI.
- Use `data.search_meta.fallback_applied` to show a hint like “We widened the search for better matches.”
- Handle `AI_OUT_OF_SCOPE` with a friendly message telling the user the advisor only supports phone shopping requests.
- Do not treat empty `products` as an error; it is a valid successful response.
