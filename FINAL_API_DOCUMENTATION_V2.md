# FINAL API DOCUMENTATION V2

This document provides a highly structured, enterprise-grade specification for the REST API (`v1`). 
It serves as the definitive implementation contract for mobile (Flutter) developers.

## Global Concepts

### Pagination Metadata
When paginated, endpoints return a `meta` block:
```json
"meta": {
  "current_page": 1,
  "last_page": 3,
  "per_page": 15,
  "total": 45,
  "has_more_pages": true
}
```
* **Tip for Flutter:** Always use `has_more_pages` to trigger infinite scrolling and append `?page=X` to load the next chunk.

### Standard Error Schema
```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```
* **Tip for Flutter:** Handle branching logic using the `code` field (e.g., `UNAUTHENTICATED`, `VALIDATION_ERROR`, `RATE_LIMIT_EXCEEDED`). Use the `message` field (or `errors` arrays) for UI presentation.

---

## ðŸ” Auth

### ðŸ”¹ Endpoint: Register User

**Method:** POST  
**URI:** `/api/v1/auth/register`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `application/json`

| Field                 | Type   | Required | Description |
| --------------------- | ------ | -------- | ----------- |
| name                  | string | Yes      | User full name |
| email                 | string | Yes      | Unique user email |
| phone                 | string | No       | User phone number |
| password              | string | Yes      | Password with minimum 8 characters |
| password_confirmation | string | Yes      | Must match `password` |

#### Example Request
```json
{
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "phone": "0999999999",
  "password": "password123",
  "password_confirmation": "password123"
}
```

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "token": "1|abc123token",
    "token_type": "Bearer",
    "user": {
      "id": 5,
      "name": "Ahmed",
      "email": "ahmed@example.com",
      "username": null,
      "phone": "0999999999",
      "role": "user",
      "wallet_balance": 0,
      "location": null
    }
  },
  "message": "Registered successfully."
}
```
**Explanation:** Creates the account, hashes the password, and immediately returns a Sanctum bearer token for mobile use.

---
### âŒ Validation Error Response
```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

---
### ðŸ“ Notes
* **Mobile Tip:** Use the returned `token` immediately for authenticated requests. No redirect or session cookie is required.

---
### ðŸ”¹ Endpoint: User Login

**Method:** POST  
**URI:** `/api/v1/auth/login`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `application/json`

| Field       | Type   | Required | Description |
| ----------- | ------ | -------- | ----------- |
| email       | string | Yes      | User email  |
| password    | string | Yes      | Plain-text password |
| device_name | string | Yes      | Identifier for the device (e.g., 'flutter-ios') |

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "token": "1|abc123token",
    "token_type": "Bearer",
    "user": {
      "id": 5,
      "name": "Ahmed",
      "email": "ahmed@example.com",
      "username": "ahmed",
      "phone": "0999999999",
      "role": "user",
      "wallet_balance": 250.0,
      "location": "Damascus"
    }
  },
  "message": "Authenticated successfully."
}
```
**Explanation:** Returns the Sanctum auth token and basic user details. Store `token` securely on the device.

---
### âŒ Error Response
```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---
### ðŸ“ Notes
* **Mobile Tip:** Store the token using `flutter_secure_storage`. Inject it into all future requests using Dio interceptors or http middleware.

---

### ðŸ”¹ Endpoint: Current Auth User

**Method:** GET  
**URI:** `/api/v1/auth/me`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "user": {
      "id": 5,
      "name": "Ahmed",
      "email": "ahmed@example.com",
      "username": "ahmed",
      "phone": "0999999999",
      "role": "user",
      "wallet_balance": 250.0,
      "location": "Damascus"
    }
  },
  "message": "Authenticated user retrieved successfully."
}
```
**Explanation:** Returns compact user data. Used on app startup to re-validate token viability.

---
### âŒ Error Response
```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

---

### ðŸ”¹ Endpoint: User Logout

**Method:** POST  
**URI:** `/api/v1/auth/logout`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": null,
  "message": "Logged out successfully."
}
```

---
### âŒ Error Response
*(Standard 401 Unauthenticated)*

---
### ðŸ“ Notes
* **Mobile Tip:** Call this, then immediately clear the token from device secure storage.

---

## ðŸ  Home

### ðŸ”¹ Endpoint: Home Feed

**Method:** GET  
**URI:** `/api/v1/home`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "categories": [
      {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone",
        "description": "Apple iPhone devices",
        "icon": "fa-mobile-screen",
        "products_count": 14
      }
    ],
    "featured_products": [
      {
        "id": 31,
        "name": "iPhone 13",
        "brand": "Apple",
        "model": "13",
        "price": 300.0,
        "condition": "used",
        "status": "available",
        "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
        "category": { "id": 2, "name": "iPhone", "slug": "iphone" },
        "seller": { "id": 8, "name": "Mohammad", "username": "seller01" }
      }
    ],
    "device_requests": [
      {
        "id": 1,
        "brand": "Apple",
        "model": "iPhone 15 Pro",
        "notes": "Looking for sealed unit only",
        "status": "approved",
        "created_at": "2026-05-04T12:00:00.000000Z",
        "user": { "id": 5, "name": "Ahmad", "username": "ahmad_99" }
      }
    ]
  },
  "message": "Home feed retrieved successfully."
}
```
**Explanation:** Loads everything needed for the main landing screen.
- `categories`: array of category objects.
- `featured_products`: array of the top 10 available products.
- `device_requests`: array of the 10 latest approved user requests.

---
### âŒ Error Response
*(Standard Server Error)*

---

## ðŸ”Ž Search

### ðŸ”¹ Endpoint: Global Search

**Method:** GET  
**URI:** `/api/v1/search?q={query}`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

**Query Params:**
| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| q     | string | Yes      | Search text |

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "products": {
      "data": [ ... ],
      "meta": { "current_page": 1, "last_page": 1, "total": 3, "has_more_pages": false }
    },
    "device_requests": {
      "data": [ ... ],
      "meta": { "current_page": 1, "last_page": 1, "total": 0, "has_more_pages": false }
    },
    "query": "iphone"
  },
  "message": "Search results retrieved successfully."
}
```
**Explanation:** Returns two separate paginated collections inside `data` (products and requests).

---
### ðŸ“ Notes
* **Mobile Tip:** Use `meta` inside `products` or `device_requests` to handle pagination separately if required.

---

## ðŸ“ˆ Dashboard

### ðŸ”¹ Endpoint: User Dashboard Stats

**Method:** GET  
**URI:** `/api/v1/me/dashboard`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "total_listings": 15,
    "active_listings": 12,
    "total_orders": 4,
    "total_sales": 8,
    "wallet_balance": 250.0
  },
  "message": "Dashboard stats retrieved successfully."
}
```
**Explanation:** Pre-aggregated metrics.
- `total_listings`: integer.
- `wallet_balance`: float.

---

## ðŸ‘¤ Profile

### ðŸ”¹ Endpoint: Get Profile

**Method:** GET  
**URI:** `/api/v1/me`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "id": 5,
    "name": "Ahmed",
    "email": "ahmed@example.com",
    "username": "ahmed",
    "phone": "0999999999",
    "gender": null,
    "date_of_birth": null,
    "location": "Damascus",
    "status": "active",
    "role": "user",
    "wallet_balance": 250.0,
    "email_verified_at": "2026-04-10T09:00:00Z",
    "created_at": "2026-04-01T12:00:00Z",
    "updated_at": "2026-04-13T10:00:00Z"
  },
  "message": "Profile retrieved successfully."
}
```

---

### ðŸ”¹ Endpoint: Update Profile

**Method:** PATCH  
**URI:** `/api/v1/me`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `application/json`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| name  | string | Yes      | Max 255 |
| email | string | Yes      | Valid, unique email format |

---
### ðŸ“¤ Success Response
*(Returns the same JSON payload as Get Profile)*

---

### ðŸ”¹ Endpoint: Delete Account

**Method:** DELETE  
**URI:** `/api/v1/me`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": null,
  "message": "Account deleted successfully."
}
```
---
### ðŸ“ Notes
* **Compliance:** Required by Apple/Google guidelines. Completely destructs profile and invalidates tokens.

---

## ðŸ›’ Products

### ðŸ”¹ Endpoint: List Products

**Method:** GET  
**URI:** `/api/v1/products`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

**Query Params:**
| Field       | Type    | Required | Description |
| ----------- | ------- | -------- | ----------- |
| category_id | integer | No       | Filter by category ID |
| source      | string  | No       | `inventory` or `user` |
| status      | string  | No       | Exact status matching (e.g. `available`) |
| page        | integer | No       | Pagination page |

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device",
      "price": 300.0,
      "condition": "used",
      "status": "available",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "seller": { "id": 8, "name": "Mohammad", "username": "seller01" },
      "category": { "id": 2, "name": "iPhone", "slug": "iphone" },
      "images": [ { "id": 90, "url": "https://...", "is_primary": false } ],
      "variants": []
    }
  ],
  "message": "Products retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 60,
    "has_more_pages": true
  }
}
```

---

### ðŸ”¹ Endpoint: Product Details

**Method:** GET  
**URI:** `/api/v1/products/{id}`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
*(Returns a single object matching the schema inside the `data` array of List Products)*

---
### âŒ Error Response
*(Standard 404 Not Found)*

---

### ðŸ”¹ Endpoint: Categories

**Method:** GET  
**URI:** `/api/v1/categories`  
**Authentication:** Not Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone",
      "description": "Apple devices",
      "icon": "fa-mobile-screen",
      "products_count": 14
    }
  ],
  "message": "Categories retrieved successfully."
}
```

---

## ðŸ·ï¸ Listings

### ðŸ”¹ Endpoint: My Listings

**Method:** GET  
**URI:** `/api/v1/me/listings`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
*(Returns Paginated array of the user's products. Identical payload structure to GET /products)*

---

### ðŸ”¹ Endpoint: Create Listing

**Method:** POST  
**URI:** `/api/v1/me/listings`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `multipart/form-data`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| brand | string | Yes      | Phone brand |
| model | string | Yes      | Phone model |
| category_id | integer | Yes | Valid category ID |
| price | number | Yes      | Min 0 |
| condition | string | Yes  | `new` or `used` |
| color | string | Yes      | Color name |
| location | string | Yes   | City/Location |
| description | string | No | Details |
| condition_notes | string | No | Scratches, etc |
| accessories | string | No | Box, charger, etc |
| disassembled_is | boolean | No | Send as 0 or 1 |
| images[] | file array | Yes | Array of files (`jpeg`, `png`, `webp`). Max 5. |

ðŸ‘‰ **Handling `images[]` in Flutter:**
Use a `MultipartRequest`. Loop through your images and add them exactly using the key `'images[]'`:
```dart
for (var file in files) {
   request.files.add(await http.MultipartFile.fromPath('images[]', file.path));
}
```

---
### ðŸ“¤ Success Response
*(Returns the created Product object)*

---

### ðŸ”¹ Endpoint: Update Listing

**Method:** POST  
**URI:** `/api/v1/me/listings/{id}/update`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `multipart/form-data`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| brand | string | Yes      | - |
| model | string | Yes      | - |
| category_id | integer | Yes | - |
| price | number | Yes      | - |
| condition | string | Yes  | `new` or `used` |
| color | string | Yes      | - |
| status | string | No | `available`, `sold`, `hidden`, `pending`, `rejected` |
| delete_images[] | integer array | No | IDs of existing images to delete |
| images[] | file array | No | New images to append |

---
### ðŸ“¤ Success Response
*(Returns the updated Product object)*

---
### âŒ Error Response
*(Standard 422 if Total Images (Existing - Deleted + New) exceeds 5)*

---
### ðŸ“ Notes
* **Mobile Tip:** Notice this is a **POST** request, not PATCH. PHP safely parses `multipart/form-data` exclusively on POST. 

---

### ðŸ”¹ Endpoint: Delete Listing

**Method:** DELETE  
**URI:** `/api/v1/me/listings/{id}`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": null,
  "message": "Listing deleted successfully."
}
```

---

## ðŸ“± Device Requests

### ðŸ”¹ Endpoint: List Device Requests

**Method:** GET  
**URI:** `/api/v1/device-requests`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": 1,
      "brand": "Apple",
      "model": "iPhone 15 Pro",
      "notes": "Looking for sealed unit only",
      "status": "approved",
      "created_at": "2026-05-04T12:00:00.000000Z",
      "user": {
        "id": 5,
        "name": "Ahmad",
        "username": "ahmad_99"
      }
    }
  ],
  "message": "Device requests retrieved successfully.",
  "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1, "has_more_pages": false }
}
```
**Explanation:** Shows requests published by other users seeking specific devices.

---

### ðŸ”¹ Endpoint: Create Device Request

**Method:** POST  
**URI:** `/api/v1/device-requests`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `application/json`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| brand | string | Yes      | - |
| model | string | Yes      | - |
| notes | string | No       | Up to 1000 chars |

---
### ðŸ“¤ Success Response
*(Returns the created Device Request object)*

---

### ðŸ”¹ Endpoint: Offer Device Fulfillment

**Method:** POST  
**URI:** `/api/v1/device-requests/{id}/offer`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": null,
  "message": "Offer sent successfully to the requester."
}
```

---
### âŒ Error Response
**Forbidden (Offering on your own request):**
```json
{
  "message": "You cannot offer on your own request.",
  "code": "FORBIDDEN"
}
```
**Already Offered:**
```json
{
  "message": "You have already sent an offer for this request.",
  "code": "ALREADY_OFFERED"
}
```

---

## ðŸ“¦ Orders

### ðŸ”¹ Endpoint: My Purchases

**Method:** GET  
**URI:** `/api/v1/orders`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": 14,
      "order_type": "user",
      "status": "pending",
      "payment_method": "wallet",
      "total_price": 300.0,
      "shipping_address": "Damascus",
      "approvals": { "seller": null, "admin": true },
      "created_at": "2026-04-13T10:15:30Z",
      "updated_at": "2026-04-13T10:15:30Z",
      "buyer": { "id": 5, "name": "Ahmed", "username": "ahmed" },
      "seller": { "id": 8, "name": "Mohammad", "username": "seller01" },
      "product": { "id": 31, "name": "iPhone 13", "brand": "Apple" },
      "variant": null
    }
  ],
  "message": "Orders retrieved successfully.",
  "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 1, "has_more_pages": false }
}
```

---

### ðŸ”¹ Endpoint: Create Order

**Method:** POST  
**URI:** `/api/v1/orders`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `application/json`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| product_id | integer | Yes | Product to buy |
| shipping_address | string | Yes | Where to ship |
| payment_method | string | Yes | `wallet`, `stripe`, `cod` |
| color | integer | No | Product variant ID (if applicable) |

---
### ðŸ“¤ Success Response
*(Returns the created Order Object)*

---
### âŒ Error Response
```json
{
  "message": "Ø±ØµÙŠØ¯ Ø§Ù„Ù…Ø­ÙØ¸Ø© ØºÙŠØ± ÙƒØ§ÙÙŠ. ÙŠØ±Ø¬Ù‰ Ø´Ø­Ù† Ø§Ù„Ø±ØµÙŠØ¯ Ø£Ùˆ Ø§Ø®ØªÙŠØ§Ø± Ø·Ø±ÙŠÙ‚Ø© Ø¯ÙØ¹ Ø£Ø®Ø±Ù‰.",
  "code": "ORDER_WALLET_BALANCE_INSUFFICIENT"
}
```

---

### ðŸ”¹ Endpoint: Order Details

**Method:** GET  
**URI:** `/api/v1/orders/{id}`  
**Authentication:** Required

---
*(Follows standard resource lookup pattern. Returns 403 if accessed by wrong user).*

---

### ðŸ”¹ Endpoint: My Sales Orders

**Method:** GET  
**URI:** `/api/v1/sales/orders`  
**Authentication:** Required

---
*(Returns paginated list of Orders where the current user is the `seller`)*

---

### ðŸ”¹ Endpoint: Approve Sale

**Method:** POST  
**URI:** `/api/v1/sales/orders/{id}/approve`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
*(None)*

---
### ðŸ“¤ Success Response
*(Returns the updated Order Object with `approvals.seller = true` and `status = approved`)*

---
### âŒ Error Response
```json
{
  "message": "ÙŠØ¬Ø¨ Ø§Ù†ØªØ¸Ø§Ø± Ù…ÙˆØ§ÙÙ‚Ø© Ø§Ù„Ø¥Ø¯Ø§Ø±Ø© Ø£ÙˆÙ„Ø§Ù‹.",
  "code": "ORDER_ADMIN_APPROVAL_REQUIRED"
}
```

---

### ðŸ”¹ Endpoint: Reject Sale

**Method:** POST  
**URI:** `/api/v1/sales/orders/{id}/reject`  
**Authentication:** Required

---
*(Returns the updated Order Object with `status = rejected`)*

---

## ðŸ’³ Wallet

### ðŸ”¹ Endpoint: Wallet Summary

**Method:** GET  
**URI:** `/api/v1/wallet`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "user_id": 5,
    "balance": 250.0,
    "transactions_count": 12,
    "recharge_requests_count": 3,
    "pending_recharge_requests_count": 1
  },
  "message": "Wallet retrieved successfully."
}
```

---

### ðŸ”¹ Endpoint: Wallet Transactions

**Method:** GET  
**URI:** `/api/v1/wallet/transactions`  
**Authentication:** Required

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": 44,
      "type": "debit",
      "amount": 300.0,
      "balance_before": 550.0,
      "balance_after": 250.0,
      "reason": "marketplace_purchase",
      "description": "Wallet payment for order #14",
      "created_at": "2026-04-13T10:20:00Z"
    }
  ],
  "message": "Wallet transactions retrieved successfully.",
  "meta": { "current_page": 1, "last_page": 1, "per_page": 10, "total": 1, "has_more_pages": false }
}
```
**Explanation:** Paginated ledger of deposits/debits affecting balance directly.

---

### ðŸ”¹ Endpoint: Recharge Requests

**Method:** GET  
**URI:** `/api/v1/wallet/recharge-requests`  
**Authentication:** Required

---
*(Returns Paginated list of user-submitted pending/approved recharge requests)*

---

### ðŸ”¹ Endpoint: Create Recharge Request

**Method:** POST  
**URI:** `/api/v1/wallet/recharge-requests`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¥ Request Body
* **Content-Type:** `multipart/form-data`

| Field | Type   | Required | Description |
| ----- | ------ | -------- | ----------- |
| amount | float | Yes      | Min 1 |
| method | string | Yes     | `syriatel_cash`, `mtn_cash`, `stripe` |
| proof  | file   | No      | Image receipt (`jpeg`, `png`, max 2048 KB) |

---
### ðŸ“¤ Success Response
*(Returns the created Recharge Request object)*

---
### ðŸ“ Notes
* Creating a recharge request does **not** instantly add balance. It must be approved by an Admin workflow first.

---

## ðŸ”” Notifications

### ðŸ”¹ Endpoint: List Notifications

**Method:** GET  
**URI:** `/api/v1/notifications`  
**Authentication:** Required

---
### ðŸ§¾ Headers
| Key           | Value            | Required |
| ------------- | ---------------- | -------- |
| Authorization | Bearer {token}   | Yes      |
| Accept        | application/json | Yes      |

---
### ðŸ“¤ Success Response
```json
{
  "data": [
    {
      "id": "uuid-1234",
      "title": "ØªÙ… Ø§Ø³ØªÙ„Ø§Ù… Ø·Ù„Ø¨Ùƒ",
      "message": "ØªÙ… Ø§Ø³ØªÙ„Ø§Ù… Ø·Ù„Ø¨Ùƒ #14 Ø¨Ù†Ø¬Ø§Ø­.",
      "type": "order",
      "is_read": false,
      "read_at": null,
      "created_at": "2026-04-13T10:15:30Z",
      "meta": { "order_id": 14 },
      "has_action": true
    }
  ],
  "message": "Notifications retrieved successfully.",
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 1, "has_more_pages": false }
}
```
**Explanation:** `has_action` indicates if the Flutter app should look inside `meta` for navigation IDs.

---

### ðŸ”¹ Endpoint: Mark As Read

**Method:** POST  
**URI:** `/api/v1/notifications/{id}/read`  
**Authentication:** Required

---
*(Returns the updated Notification object with `is_read = true`)*

---

### ðŸ”¹ Endpoint: Mark All As Read

**Method:** POST  
**URI:** `/api/v1/notifications/read-all`  
**Authentication:** Required

---
### ðŸ“¤ Success Response
```json
{
  "data": {
    "updated_count": 4
  },
  "message": "Notifications marked as read successfully."
}
```

