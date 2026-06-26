# DEVICE COMPARISON & AI SEARCH API DOCUMENTATION

This document provides a structured, production-grade specification for:

- AI-powered phone advisor
- Master device catalog
- Device comparison API

It is intended for Flutter developers integrating search, device discovery, and side-by-side comparison into the mobile application.

## Global Concepts

### Pagination Metadata
Paginated endpoints return a `meta` block:
```json
"meta": {
  "current_page": 1,
  "last_page": 3,
  "per_page": 15,
  "total": 45,
  "from": 1,
  "to": 15,
  "has_more_pages": true
}
```
* **Tip for Flutter:** Use `has_more_pages` for infinite scrolling and append query params such as `?page=2&per_page=15` when loading more data.

### Standard Success Envelope
```json
{
  "data": {},
  "message": "Request completed successfully."
}
```

### Standard Error Schema
```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "field_name": [
      "Validation message"
    ]
  }
}
```
* **Tip for Flutter:** Branch by `code` for application logic, and use `message` or `errors` arrays for UI presentation.

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
        "device": {
          "id": 6,
          "brand": "Apple",
          "model_name": "iPhone 13",
          "slug": "apple-iphone-13"
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
**Explanation:** Gemini successfully interpreted the request and returned valid filters. Those filters were applied to marketplace products.

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
        "device": {
          "id": 2,
          "brand": "Samsung",
          "model_name": "Galaxy S24 Ultra",
          "slug": "samsung-galaxy-s24-ultra"
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
      "provider_failure_code": "AI_PROVIDER_ERROR"
    }
  },
  "message": "Fallback search used due to AI unavailability."
}
```
**Explanation:** Gemini was unavailable after retries. The backend switched to raw-query fallback search so the user still receives results.

---
### ❌ Error Responses

#### Validation Error (`422`)
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

#### Out Of Scope (`422`)
```json
{
  "message": "The request is خارج نطاق البحث عن الهواتف.",
  "code": "AI_OUT_OF_SCOPE"
}
```
Examples:
- `احسبلي 2 + 2`
- `Write me a poem`
- `أريد هاتفاً ثم احجز لي فندقاً`

#### AI Invalid Filters (`422`)
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

#### AI Invalid JSON (`422`)
```json
{
  "message": "AI returned invalid JSON filters.",
  "code": "AI_INVALID_JSON",
  "errors": {
    "raw_response": "Some malformed provider output"
  }
}
```

#### AI Invalid Response (`422`)
```json
{
  "message": "AI response did not contain a valid JSON block.",
  "code": "AI_INVALID_RESPONSE"
}
```

#### AI Provider Error (`502`)
```json
{
  "message": "AI provider request failed.",
  "code": "AI_PROVIDER_ERROR"
}
```

#### AI Provider Unavailable (`503`)
```json
{
  "message": "AI provider is temporarily unavailable.",
  "code": "AI_PROVIDER_UNAVAILABLE"
}
```

#### AI Configuration Error (`500`)
```json
{
  "message": "AI service is not configured correctly.",
  "code": "AI_CONFIGURATION_ERROR"
}
```

---
### 📝 Notes
* `filters` contains normalized AI output when Gemini succeeds.
* `filters` becomes `null` only when provider fallback search is used.
* `fallback = true` means the endpoint completed successfully through non-AI fallback logic.
* `search_meta.fallback_applied = true` may also appear when AI succeeds but product matching had to be widened internally.
* The backend retries Gemini up to 3 times before switching to fallback.
* `provider_failure_code` inside `search_meta` is informational only. Current values may include `AI_PROVIDER_ERROR` or `AI_PROVIDER_UNAVAILABLE`.
* The request field `query` is required and currently validated as a string with minimum length `2` and maximum length `1000`.
* For Flutter error handling, rely on the `code` field rather than exact localized `message` text.
* Empty `products: []` is a valid successful response and should not be treated as an API failure.

---

## 📱 Device Catalog

### 🔹 Endpoint: List Devices

**Method:** GET  
**URI:** `/api/v1/devices`  
**Authentication:** Not Required

---
### 🧾 Headers
| Key    | Value            | Required |
| ------ | ---------------- | -------- |
| Accept | application/json | Yes      |

---
### 📥 Request Body
*(None)*

#### Query Parameters
| Field    | Type   | Required | Description |
| -------- | ------ | -------- | ----------- |
| q        | string | No       | Search by brand, model, processor, or display |
| brand    | string | No       | Filter by brand |
| per_page | int    | No       | Number of devices per page. Default: `15` |
| page     | int    | No       | Pagination page number |

#### Example Request
`GET /api/v1/devices?q=iphone&per_page=10`

---
### 📤 Success Response
```json
{
  "data": [
    {
      "id": 6,
      "brand": "Apple",
      "model_name": "iPhone 13",
      "slug": "apple-iphone-13",
      "name": "Apple iPhone 13",
      "image_url": "https://images.unsplash.com/photo-1632661674596-df8be070a5c5?auto=format&fit=crop&w=900&q=80",
      "release_year": 2021,
      "marketplace_products_count": 4,
      "specifications": {
        "battery": "3240mAh",
        "camera": "12MP main + 12MP ultrawide",
        "storage": "128GB / 256GB / 512GB",
        "ram": "4GB",
        "processor": "Apple A15 Bionic",
        "performance": "High-end",
        "display": "6.1-inch Super Retina XDR OLED",
        "operating_system": "iOS 15"
      }
    }
  ],
  "message": "Devices retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1,
    "from": 1,
    "to": 1,
    "has_more_pages": false
  }
}
```
**Explanation:** Returns devices from the master device database, not marketplace listings.

---
### ❌ Error Responses
No custom error response for normal usage. Standard Laravel/API validation and routing errors apply.

---
### 📝 Notes
* This endpoint is backed by the normalized master device catalog.
* `marketplace_products_count` indicates how many marketplace listings are mapped to that device.
* Use this endpoint to populate compare selectors in Flutter.

---
### 🔹 Endpoint: Device Details

**Method:** GET  
**URI:** `/api/v1/devices/{id}`  
**Authentication:** Not Required

---
### 🧾 Headers
| Key    | Value            | Required |
| ------ | ---------------- | -------- |
| Accept | application/json | Yes      |

---
### 📥 Request Body
*(None)*

#### Path Parameters
| Field | Type | Required | Description |
| ----- | ---- | -------- | ----------- |
| id    | int  | Yes      | Device ID from the master device catalog |

---
### 📤 Success Response
```json
{
  "data": {
    "id": 2,
    "brand": "Samsung",
    "model_name": "Galaxy S24 Ultra",
    "slug": "samsung-galaxy-s24-ultra",
    "name": "Samsung Galaxy S24 Ultra",
    "image_url": "https://images.unsplash.com/photo-1706275397080-6dbed9bf7178?auto=format&fit=crop&w=900&q=80",
    "release_year": 2024,
    "marketplace_products_count": 2,
    "specifications": {
      "battery": "5000mAh",
      "camera": "200MP main + 12MP ultrawide + 50MP telephoto + 10MP telephoto",
      "storage": "256GB / 512GB / 1TB",
      "ram": "12GB",
      "processor": "Snapdragon 8 Gen 3",
      "performance": "Flagship",
      "display": "6.8-inch Dynamic AMOLED 2X 120Hz",
      "operating_system": "Android 14"
    }
  },
  "message": "Device retrieved successfully."
}
```

---
### ❌ Error Responses

#### Not Found (`404`)
```json
{
  "message": "Resource not found.",
  "code": "NOT_FOUND"
}
```

---
### 📝 Notes
* Use this endpoint when a detailed device profile screen is needed before comparison.

---

## ⚖️ Device Comparison

### 🔹 Endpoint: Compare Devices

**Method:** POST  
**URI:** `/api/v1/compare`  
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

| Field        | Type  | Required | Description |
| ------------ | ----- | -------- | ----------- |
| device_ids   | array | Yes      | Exactly 2 device IDs from the master device catalog |
| device_ids.* | int   | Yes      | Individual device ID. Values must be distinct and exist in `devices` |

#### Example Request
```json
{
  "device_ids": [2, 6]
}
```

---
### 📤 Success Response
```json
{
  "data": {
    "devices": [
      {
        "id": 2,
        "brand": "Samsung",
        "model_name": "Galaxy S24 Ultra",
        "slug": "samsung-galaxy-s24-ultra",
        "name": "Samsung Galaxy S24 Ultra",
        "image_url": "https://images.unsplash.com/photo-1706275397080-6dbed9bf7178?auto=format&fit=crop&w=900&q=80",
        "release_year": 2024,
        "marketplace_products_count": 2,
        "specifications": {
          "battery": "5000mAh",
          "camera": "200MP main + 12MP ultrawide + 50MP telephoto + 10MP telephoto",
          "storage": "256GB / 512GB / 1TB",
          "ram": "12GB",
          "processor": "Snapdragon 8 Gen 3",
          "performance": "Flagship",
          "display": "6.8-inch Dynamic AMOLED 2X 120Hz",
          "operating_system": "Android 14"
        }
      },
      {
        "id": 6,
        "brand": "Apple",
        "model_name": "iPhone 13",
        "slug": "apple-iphone-13",
        "name": "Apple iPhone 13",
        "image_url": "https://images.unsplash.com/photo-1632661674596-df8be070a5c5?auto=format&fit=crop&w=900&q=80",
        "release_year": 2021,
        "marketplace_products_count": 4,
        "specifications": {
          "battery": "3240mAh",
          "camera": "12MP main + 12MP ultrawide",
          "storage": "128GB / 256GB / 512GB",
          "ram": "4GB",
          "processor": "Apple A15 Bionic",
          "performance": "High-end",
          "display": "6.1-inch Super Retina XDR OLED",
          "operating_system": "iOS 15"
        }
      }
    ],
    "rows": [
      {
        "key": "battery",
        "label": "Battery",
        "values": ["5000mAh", "3240mAh"],
        "different": true
      },
      {
        "key": "camera",
        "label": "Camera",
        "values": [
          "200MP main + 12MP ultrawide + 50MP telephoto + 10MP telephoto",
          "12MP main + 12MP ultrawide"
        ],
        "different": true
      },
      {
        "key": "storage",
        "label": "Storage",
        "values": [
          "256GB / 512GB / 1TB",
          "128GB / 256GB / 512GB"
        ],
        "different": true
      },
      {
        "key": "ram",
        "label": "RAM",
        "values": ["12GB", "4GB"],
        "different": true
      },
      {
        "key": "processor",
        "label": "Processor",
        "values": ["Snapdragon 8 Gen 3", "Apple A15 Bionic"],
        "different": true
      },
      {
        "key": "performance",
        "label": "Performance",
        "values": ["Flagship", "High-end"],
        "different": true
      },
      {
        "key": "display",
        "label": "Display",
        "values": [
          "6.8-inch Dynamic AMOLED 2X 120Hz",
          "6.1-inch Super Retina XDR OLED"
        ],
        "different": true
      },
      {
        "key": "operating_system",
        "label": "Operating System",
        "values": ["Android 14", "iOS 15"],
        "different": true
      }
    ]
  },
  "message": "Comparison generated successfully."
}
```
**Explanation:** Returns two normalized devices and a side-by-side row structure optimized for table rendering in Flutter or web.

---
### ❌ Error Responses

#### Validation Error (`422`)
```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "device_ids": [
      "The device ids field must have 2 items."
    ]
  }
}
```

Possible validation cases:
- `device_ids` missing
- `device_ids` is not an array
- array contains fewer than 2 or more than 2 items
- duplicate device IDs
- one or both IDs do not exist in `devices`

---
### 📝 Notes
* The compare endpoint works only with the master `devices` catalog, not raw marketplace product IDs.
* `rows` is intentionally simple for Flutter table rendering:
  - `label` = row title
  - `values[0]` = Device A value
  - `values[1]` = Device B value
  - `different` = whether the two values differ
* The backend normalizes specification keys so Flutter can rely on stable labels:
  - `battery`
  - `camera`
  - `storage`
  - `ram`
  - `processor`
  - `performance`
  - `display`
  - `operating_system`
* `marketplace_products_count` can be used to show how many marketplace listings exist for each master device.

---
## Flutter Integration Checklist

1. Use `GET /api/v1/devices` to populate compare selectors.
2. Store selected device IDs locally in the compare screen state.
3. Send selected IDs to `POST /api/v1/compare`.
4. Render `data.devices` for the comparison header cards.
5. Render `data.rows` directly into a side-by-side comparison table.
6. Use `different = true` to visually highlight differing specs.
7. For AI search, read marketplace products from `data.products`.
8. Treat `fallback = true` in AI search as a successful response, not an error.
9. Treat empty arrays as valid data states, not crashes.
