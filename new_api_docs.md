# AI SEARCH API DOCUMENTATION

This document provides a structured, production-grade specification for the AI-powered search endpoint.
It is intended for Flutter developers integrating the phone advisor experience in mobile applications.

The endpoint is built on top of the existing REST API (`v1`) and returns responses in the same global API envelope used by the rest of the platform.

## Global Concepts

### Standard Success Envelope
```json
{
  "data": {},
  "message": "Request completed successfully."
}
```

### Standard Error Envelope
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

### AI Search Behavior Summary
The AI advisor processes the request in this order:

1. Accept the user's natural-language phone shopping query
2. Send the query to Gemini to generate structured filters
3. Query products using those filters
4. If strict matching returns no products, broaden the search internally
5. If Gemini is unavailable after retries, switch to fallback search using the raw query

**Tip for Flutter:** Always branch on response content, not assumptions:
- If `data.fallback` is `false`, the AI path completed successfully
- If `data.fallback` is `true`, fallback search was used
- If the API returns `422` with `code = AI_OUT_OF_SCOPE`, show a friendly message telling the user the advisor only supports phone shopping requests

---

## 🤖 AI Search

### 🔹 Endpoint: AI Phone Advisor

**Method:** POST  
**URI:** `/api/v1/ai/advisor`  
**Authentication:** Not Required

---
### 🧾 Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |
| Content-Type  | application/json | Yes      |

---
### 📥 Request Body
* **Content-Type:** `application/json`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| query | string | Yes      | Natural-language phone shopping query in Arabic or English |

#### Example Request
```json
{
  "query": "بدي موبايل قوي للألعاب بسعر لا يتجاوز 450$"
}
```

#### More Valid Query Examples
```json
{
  "query": "I need a used iPhone with strong camera"
}
```

```json
{
  "query": "هاتف بطارية قوية للاستخدام اليومي"
}
```

```json
{
  "query": "Samsung phone for gaming under 500 dollars"
}
```

---
### 📤 Success Response
#### Example: AI Path Succeeded
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
    "fallback": false,
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

**Explanation:** Gemini successfully interpreted the request and returned valid filters. Those filters were applied to the products table and at least one matching product was found.

---
### 📤 Success Response
#### Example: AI Succeeded But Internal Search Was Broadened
```json
{
  "data": {
    "filters": {
      "brand": "Apple",
      "price_max": 120
    },
    "products": [],
    "fallback": false,
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

**Explanation:** Gemini succeeded, but the strict filter set did not return products. The backend broadened the search internally. This is **not** provider fallback. The request still completed through the AI path.

---
### 📤 Success Response
#### Example: Provider Fallback Search Used
```json
{
  "data": {
    "filters": null,
    "products": [
      {
        "id": 18,
        "name": "Samsung Galaxy S23",
        "slug": "samsung-galaxy-s23-18",
        "brand": "Samsung",
        "model": "Galaxy S23",
        "description": "High-performance device suitable for gaming.",
        "defects": null,
        "condition_notes": null,
        "accessories": "Original charger",
        "disassembled_is": false,
        "reason_disassembly": null,
        "price": 480,
        "condition": "used",
        "status": "available",
        "source": "user",
        "color": "Black",
        "location": "Aleppo",
        "primary_image_url": "http://localhost/storage/products/s23.jpg",
        "created_at": "2026-06-25T11:15:00.000000Z",
        "updated_at": "2026-06-25T11:15:00.000000Z",
        "seller": {
          "id": 9,
          "name": "Khaled",
          "username": "khaled",
          "location": "Aleppo"
        },
        "category": {
          "id": 3,
          "name": "Samsung",
          "slug": "samsung"
        },
        "images": [
          {
            "id": 51,
            "url": "http://localhost/storage/products/s23.jpg",
            "is_primary": true
          }
        ],
        "variants": []
      }
    ],
    "fallback": true,
    "search_meta": {
      "fallback_applied": true,
      "match_strategy": "provider_fallback",
      "result_count": 1,
      "applied_filters": {
        "price_max": 495
      },
      "relaxed_filters": [],
      "raw_query": "بدي موبايل قوي للألعاب بسعر 450$",
      "matched_keywords": [
        "للألعاب",
        "450"
      ],
      "provider_failure_code": "AI_PROVIDER_UNAVAILABLE"
    }
  },
  "message": "Fallback search used due to AI unavailability."
}
```

**Explanation:** Gemini did not complete successfully after retries, so the backend used raw-query fallback search instead of returning an AI error. This keeps the user experience smooth and still returns products.

---
### ❌ Error Responses

#### 1. Validation Error (`422`)
Returned when the request body is invalid.

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

Possible validation cases:
- `query` missing
- `query` shorter than 2 characters
- `query` longer than 1000 characters

---
#### 2. Out Of Scope (`422`)
Returned when the AI determines the request is not about shopping for phones.

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
- `What's the weather today?`

**Flutter Handling:** Show a friendly message such as:  
`This assistant only supports phone shopping requests.`

---
#### 3. AI Invalid Filters (`422`)
Returned when Gemini responds with malformed or unsupported filter values and fallback is **not** used for that category of failure.

```json
{
  "message": "AI returned unsupported filters.",
  "code": "AI_INVALID_FILTERS",
  "errors": {
    "performance": [
      "The selected performance is invalid."
    ]
  }
}
```

**Flutter Handling:** Treat this as a failed AI interpretation case and ask the user to rephrase the request.

---
#### 4. AI Invalid JSON (`422`)
Returned when Gemini responds with content that cannot be parsed into a valid JSON object and fallback is **not** used for that category of failure.

```json
{
  "message": "AI returned invalid JSON filters.",
  "code": "AI_INVALID_JSON",
  "errors": {
    "raw_response": "Some malformed provider output"
  }
}
```

**Flutter Handling:** Show a generic retry message such as:  
`We could not understand the request format. Please try again.`

---
#### 5. Fallback Behavior Instead Of Provider Errors (`200`)
Provider instability does **not** surface to the user as a normal API failure when fallback search succeeds.

That means cases like:
- provider timeout
- provider unreachable
- provider `503`
- provider `5xx`
- retry exhaustion

can still result in:

```json
{
  "data": {
    "filters": null,
    "products": [],
    "fallback": true
  },
  "message": "Fallback search used due to AI unavailability."
}
```

**Important:** For Flutter, this is a **successful response**, not an error response.

---
### 📝 Notes
* **Natural Language Support:** The `query` field accepts Arabic and English phone-shopping requests.
* **Allowed Filter Keys:** When AI succeeds, `filters` may include:
  - `price_max`
  - `price_min`
  - `condition`
  - `brand`
  - `performance`
  - `use_case`
* **Possible `performance` values:** `low`, `medium`, `high`
* **Possible `use_case` values:** `gaming`, `camera`, `battery`, `general`
* **Possible `condition` values:** `new`, `used`
* **Retry Logic:** The backend retries Gemini up to 3 times with a short delay between attempts before switching to fallback search.
* **Fallback Logic:** If provider-level AI processing fails completely, the backend uses the raw query to search by `name`, `brand`, `model`, `description`, `condition_notes`, and `accessories`. Numeric values in the query may be interpreted as a price hint.
* **Difference Between `fallback` and `search_meta.fallback_applied`:**
  - `fallback = true` means provider fallback search was used because the AI provider was unavailable or unstable
  - `search_meta.fallback_applied = true` can also mean the AI succeeded, but the backend widened the product search internally to improve matching
* **Empty Products Array:** `products: []` is valid and should not be treated as an API failure.
* **Mobile Recommendation:** Render the product list directly from `data.products`, and optionally show chips or badges using `data.filters` and `data.search_meta`.
* **No Authentication Required:** This endpoint is public and does not require a bearer token.

---
## Flutter Integration Checklist

Use this checklist during implementation:

1. Send `POST /api/v1/ai/advisor`
2. Set `Accept: application/json`
3. Set `Content-Type: application/json`
4. Send `{ "query": "..." }`
5. Read products from `response.data.products`
6. Read interpreted filters from `response.data.filters`
7. If `response.data.fallback == true`, show a subtle message like:
   `Showing results from fallback search`
8. If HTTP `422` with `code = AI_OUT_OF_SCOPE`, show a scope-specific message
9. If HTTP `422` with `code = VALIDATION_ERROR`, show inline input validation
10. If `products` is empty, show an empty-state UI, not an error screen
