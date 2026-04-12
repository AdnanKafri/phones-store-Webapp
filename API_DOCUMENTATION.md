# API Documentation

This document describes the current REST API implementation of the Laravel 11 marketplace application.

It is based on the actual API routes, controllers, requests, resources, and exception handling in the codebase.

## Overview

- API version: `v1`
- Base path: `/api/v1`
- Authentication: Laravel Sanctum bearer tokens
- Response format: JSON only for all `/api/*` requests
- Rate limit: `60` requests per minute per authenticated user or per IP
- Pagination: standard Laravel `?page=` query parameter

## Domains

- **Auth**: login, current authenticated user, logout
- **Orders**: buyer order creation and lookup, seller sales-order actions
- **Wallet**: wallet summary, wallet transactions, recharge requests
- **Products**: public catalog browsing
- **Listings**: authenticated user listing management
- **Notifications**: in-app notifications for the authenticated user
- **Profile**: authenticated user profile data

## Base URL

Use your project host plus the API prefix:

```text
https://your-domain.com/api/v1
```

Examples in this document use relative paths such as `/auth/login`, which means:

```text
https://your-domain.com/api/v1/auth/login
```

## Common Headers

### Public endpoints

```http
Accept: application/json
Content-Type: application/json
```

### Authenticated endpoints

```http
Accept: application/json
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
```

For upload endpoints, use `multipart/form-data` instead of `application/json`.

## Authentication

The API uses **Laravel Sanctum personal access tokens**.

### Login flow

1. Call `POST /auth/login` with `email`, `password`, and `device_name`.
2. Save the returned token securely on the device.
3. Send the token in the `Authorization` header as `Bearer <token>`.
4. Use `POST /auth/logout` to revoke the current token.

### Important auth notes

- `POST /auth/logout` revokes only the **current access token**.
- `GET /auth/me` returns a compact auth-oriented user payload.
- `GET /me` returns the full profile resource.
- Missing or invalid bearer tokens return:

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

## Response Standard

### Success format

```json
{
  "data": {},
  "message": "Request completed successfully.",
  "meta": {}
}
```

Notes:

- `meta` appears on paginated list endpoints.
- `data` may be `null` for actions like logout or delete.

### Error format

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "field_name": [
      "Validation message."
    ]
  }
}
```

Notes:

- `message` and validation text inside `errors` are human-readable and may be localized.
- Mobile clients should use the top-level `code` field as the stable machine-readable contract.

## Global Error Handling

API exceptions are always rendered as JSON.

| HTTP Status | Code | Meaning |
|---|---|---|
| `401` | `UNAUTHENTICATED` | Missing or invalid token |
| `403` | `FORBIDDEN` | Authenticated user is not allowed to access the resource |
| `404` | `NOT_FOUND` | Route model or endpoint not found |
| `405` | `METHOD_NOT_ALLOWED` | Wrong HTTP method |
| `422` | `VALIDATION_ERROR` | Validation failed |
| `429` | `RATE_LIMIT_EXCEEDED` | API rate limit exceeded |
| `500` | `SERVER_ERROR` | Unhandled server error |

### Standard error examples

#### Validation error

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

#### Forbidden

```json
{
  "message": "This action is unauthorized.",
  "code": "FORBIDDEN"
}
```

#### Not found

```json
{
  "message": "Resource not found.",
  "code": "NOT_FOUND"
}
```

## Pagination

Paginated endpoints return:

```json
{
  "data": [],
  "message": "List retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25,
    "from": 1,
    "to": 10,
    "has_more_pages": true
  }
}
```

Supported query parameter:

| Query Parameter | Type | Description |
|---|---|---|
| `page` | integer | Page number |

The API currently uses fixed page sizes per endpoint:

- Orders: `10`
- Sales orders: `10`
- Wallet transactions: `10`
- Wallet recharge requests: `10`
- Products: `12`
- Listings: `12`
- Notifications: `20`

## Authentication API

### Domain summary

Purpose:

- Authenticate mobile users
- Return the current authenticated user
- Revoke the current Sanctum token

Endpoints:

- `POST /auth/login`
- `GET /auth/me`
- `POST /auth/logout`

### POST /auth/login

Authenticate a user and issue a Sanctum token.

- Authentication required: `No`
- Headers:
  - `Accept: application/json`
  - `Content-Type: application/json`

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `email` | string | Yes | Must be a valid email address |
| `password` | string | Yes | Plain-text password |
| `device_name` | string | Yes | Max `255` characters |

#### Example request

```json
{
  "email": "ahmed@example.com",
  "password": "secret123",
  "device_name": "flutter-android"
}
```

#### Success response `200`

```json
{
  "data": {
    "token": "1|plain-text-token",
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

#### Error response `422`

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "These credentials do not match our records."
    ]
  }
}
```

#### Notes

- Login uses a credentials-based throttle in addition to the general API rate limiter.
- Failed credentials and login throttling both surface as `422 VALIDATION_ERROR`.

### GET /auth/me

Return the currently authenticated user in the compact auth payload format.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

#### Notes

- This endpoint is intentionally lightweight.
- If you need the full profile payload, use `GET /me`.

### POST /auth/logout

Revoke the current Sanctum access token.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

```json
{
  "data": null,
  "message": "Logged out successfully."
}
```

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

#### Notes

- Only the token used in the request is deleted.

## Orders API

### Domain summary

Purpose:

- Allow authenticated buyers to create and view their orders
- Allow sellers to list their marketplace sales orders
- Allow sellers to approve or reject marketplace sales orders

Dependencies:

- Authentication
- Product availability
- Wallet balance for wallet-based purchases
- Admin approval for seller approval on marketplace orders

Endpoints:

- `GET /orders`
- `GET /orders/{order}`
- `POST /orders`
- `GET /sales/orders`
- `POST /sales/orders/{order}/approve`
- `POST /sales/orders/{order}/reject`

### Order object

Order responses use this structure:

```json
{
  "id": 14,
  "order_type": "user",
  "status": "pending",
  "payment_method": "wallet",
  "total_price": 300.0,
  "shipping_address": "Damascus",
  "approvals": {
    "seller": null,
    "admin": true
  },
  "created_at": "2026-04-13T10:15:30Z",
  "updated_at": "2026-04-13T10:15:30Z",
  "buyer": {
    "id": 5,
    "name": "Ahmed",
    "username": "ahmed",
    "phone": "0999999999",
    "location": "Damascus"
  },
  "seller": {
    "id": 8,
    "name": "Mohammad",
    "username": "seller01",
    "phone": "0988888888",
    "location": "Aleppo"
  },
  "product": {
    "id": 31,
    "name": "iPhone 13",
    "slug": "iphone-13-31",
    "brand": "Apple",
    "model": "13",
    "description": "Used device in good condition",
    "price": 300.0,
    "condition": "used",
    "status": "available",
    "source": "user",
    "color": "Black",
    "location": "Aleppo",
    "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
    "category": {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone"
    },
    "images": [
      {
        "id": 90,
        "url": "https://your-domain.com/storage/products/example.jpg",
        "is_primary": false
      }
    ]
  },
  "variant": null
}
```

### Known order values

- `payment_method`: `wallet`, `stripe`, `cod`
- `order_type`: `inventory`, `user`
- `status`: `pending`, `approved`, `shipping`, `completed`, `rejected`

### GET /orders

Return the authenticated buyer's orders.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

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
      "approvals": {
        "seller": null,
        "admin": true
      },
      "created_at": "2026-04-13T10:15:30Z",
      "updated_at": "2026-04-13T10:15:30Z",
      "buyer": {
        "id": 5,
        "name": "Ahmed",
        "username": "ahmed",
        "phone": "0999999999",
        "location": "Damascus"
      },
      "seller": {
        "id": 8,
        "name": "Mohammad",
        "username": "seller01",
        "phone": "0988888888",
        "location": "Aleppo"
      },
      "product": {
        "id": 31,
        "name": "iPhone 13",
        "slug": "iphone-13-31",
        "brand": "Apple",
        "model": "13",
        "description": "Used device in good condition",
        "price": 300.0,
        "condition": "used",
        "status": "available",
        "source": "user",
        "color": "Black",
        "location": "Aleppo",
        "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
        "category": {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone"
        },
        "images": [
          {
            "id": 90,
            "url": "https://your-domain.com/storage/products/example.jpg",
            "is_primary": false
          }
        ]
      },
      "variant": null
    }
  ],
  "message": "Orders retrieved successfully.",
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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### GET /orders/{order}

Return a single buyer-owned order.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

```json
{
  "data": {
    "id": 14,
    "order_type": "user",
    "status": "pending",
    "payment_method": "wallet",
    "total_price": 300.0,
    "shipping_address": "Damascus",
    "approvals": {
      "seller": null,
      "admin": true
    },
    "created_at": "2026-04-13T10:15:30Z",
    "updated_at": "2026-04-13T10:15:30Z",
    "buyer": {
      "id": 5,
      "name": "Ahmed",
      "username": "ahmed",
      "phone": "0999999999",
      "location": "Damascus"
    },
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "phone": "0988888888",
      "location": "Aleppo"
    },
    "product": {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device in good condition",
      "price": 300.0,
      "condition": "used",
      "status": "available",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "category": {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone"
      },
      "images": [
        {
          "id": 90,
          "url": "https://your-domain.com/storage/products/example.jpg",
          "is_primary": false
        }
      ]
    },
    "variant": null
  },
  "message": "Order retrieved successfully."
}
```

#### Error response `403`

```json
{
  "message": "You are not authorized to view this order.",
  "code": "FORBIDDEN"
}
```

#### Notes

- If the order ID does not exist, the API returns `404 NOT_FOUND`.
- If the order exists but belongs to another buyer, the API returns `403 FORBIDDEN`.

### POST /orders

Create a new order for a product.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
  - `Content-Type: application/json`

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `product_id` | integer | Yes | Must exist in `products` |
| `shipping_address` | string | Yes | Max `1000` characters |
| `color` | integer | No | Variant ID from `product_variants.id` |
| `payment_method` | string | Yes | `wallet`, `stripe`, or `cod` |

#### Example request

```json
{
  "product_id": 31,
  "shipping_address": "Damascus - Mazzeh",
  "payment_method": "wallet",
  "color": 7
}
```

#### Success response `201`

```json
{
  "data": {
    "id": 14,
    "order_type": "inventory",
    "status": "pending",
    "payment_method": "wallet",
    "total_price": 500.0,
    "shipping_address": "Damascus - Mazzeh",
    "approvals": {
      "seller": true,
      "admin": null
    },
    "created_at": "2026-04-13T10:15:30Z",
    "updated_at": "2026-04-13T10:15:30Z",
    "buyer": {
      "id": 5,
      "name": "Ahmed",
      "username": "ahmed",
      "phone": "0999999999",
      "location": "Damascus"
    },
    "seller": null,
    "product": {
      "id": 41,
      "name": "Samsung A54",
      "slug": "samsung-a54-41",
      "brand": "Samsung",
      "model": "A54",
      "description": "New device",
      "price": 500.0,
      "condition": "new",
      "status": "available",
      "source": "inventory",
      "color": "Black",
      "location": "Damascus",
      "primary_image_url": "https://your-domain.com/storage/products/a54.jpg",
      "category": {
        "id": 4,
        "name": "Samsung",
        "slug": "samsung"
      },
      "images": [
        {
          "id": 120,
          "url": "https://your-domain.com/storage/products/a54.jpg",
          "is_primary": false
        }
      ]
    },
    "variant": {
      "id": 7,
      "color_name": "Black",
      "color_code": "#000000",
      "stock_quantity": 12
    }
  },
  "message": "Order created successfully."
}
```

#### Error response `422`

Validation example:

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "product_id": [
      "The selected product id is invalid."
    ]
  }
}
```

Business-rule example:

```json
{
  "message": "رصيد المحفظة غير كافي. يرجى شحن الرصيد أو اختيار طريقة دفع أخرى.",
  "code": "ORDER_WALLET_BALANCE_INSUFFICIENT"
}
```

#### Notes

- Business logic stays in the service layer.
- Known business error codes:
  - `ORDER_SELF_PURCHASE_NOT_ALLOWED`
  - `ORDER_PRODUCT_NOT_AVAILABLE`
  - `ORDER_PENDING_DUPLICATE`
  - `ORDER_WALLET_BALANCE_INSUFFICIENT`
  - `ORDER_VARIANT_OUT_OF_STOCK`
- For inventory products:
  - `order_type` becomes `inventory`
  - `seller_approval` starts as `true`
  - `admin_approval` starts as `null`
- For marketplace user listings:
  - `order_type` becomes `user`
  - both approvals start as `null`

### GET /sales/orders

Return marketplace sales orders for the authenticated seller.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

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
      "approvals": {
        "seller": null,
        "admin": true
      },
      "created_at": "2026-04-13T10:15:30Z",
      "updated_at": "2026-04-13T10:15:30Z",
      "buyer": {
        "id": 5,
        "name": "Ahmed",
        "username": "ahmed",
        "phone": "0999999999",
        "location": "Damascus"
      },
      "seller": {
        "id": 8,
        "name": "Mohammad",
        "username": "seller01",
        "phone": "0988888888",
        "location": "Aleppo"
      },
      "product": {
        "id": 31,
        "name": "iPhone 13",
        "slug": "iphone-13-31",
        "brand": "Apple",
        "model": "13",
        "description": "Used device in good condition",
        "price": 300.0,
        "condition": "used",
        "status": "available",
        "source": "user",
        "color": "Black",
        "location": "Aleppo",
        "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
        "category": {
          "id": 2,
          "name": "iPhone",
          "slug": "iphone"
        },
        "images": [
          {
            "id": 90,
            "url": "https://your-domain.com/storage/products/example.jpg",
            "is_primary": false
          }
        ]
      },
      "variant": null
    }
  ],
  "message": "Sales orders retrieved successfully.",
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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

#### Notes

- This endpoint returns only marketplace orders where `order_type = user`.

### POST /sales/orders/{order}/approve

Approve a marketplace sales order as the seller.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Request body: none

#### Success response `200`

```json
{
  "data": {
    "id": 14,
    "order_type": "user",
    "status": "approved",
    "payment_method": "wallet",
    "total_price": 300.0,
    "shipping_address": "Damascus",
    "approvals": {
      "seller": true,
      "admin": true
    },
    "created_at": "2026-04-13T10:15:30Z",
    "updated_at": "2026-04-13T10:20:00Z",
    "buyer": {
      "id": 5,
      "name": "Ahmed",
      "username": "ahmed",
      "phone": "0999999999",
      "location": "Damascus"
    },
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "phone": "0988888888",
      "location": "Aleppo"
    },
    "product": {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device in good condition",
      "price": 300.0,
      "condition": "used",
      "status": "sold",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "category": {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone"
      },
      "images": [
        {
          "id": 90,
          "url": "https://your-domain.com/storage/products/example.jpg",
          "is_primary": false
        }
      ]
    },
    "variant": null
  },
  "message": "Order approved successfully."
}
```

#### Error response `422`

```json
{
  "message": "يجب انتظار موافقة الإدارة أولاً.",
  "code": "ORDER_ADMIN_APPROVAL_REQUIRED"
}
```

#### Notes

- If the authenticated user is not the seller of the product, the API returns `403 FORBIDDEN`.
- Known business error codes:
  - `ORDER_ADMIN_APPROVAL_REQUIRED`
  - `ORDER_BUYER_BALANCE_INSUFFICIENT`
  - `ORDER_WORKFLOW_FAILED`
- When the order uses wallet payment and approval succeeds:
  - buyer wallet is debited
  - seller wallet is credited
  - order status becomes `approved`
  - product status becomes `sold`

### POST /sales/orders/{order}/reject

Reject a marketplace sales order as the seller.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Request body: none

#### Success response `200`

```json
{
  "data": {
    "id": 14,
    "order_type": "user",
    "status": "rejected",
    "payment_method": "wallet",
    "total_price": 300.0,
    "shipping_address": "Damascus",
    "approvals": {
      "seller": false,
      "admin": true
    },
    "created_at": "2026-04-13T10:15:30Z",
    "updated_at": "2026-04-13T10:20:00Z",
    "buyer": {
      "id": 5,
      "name": "Ahmed",
      "username": "ahmed",
      "phone": "0999999999",
      "location": "Damascus"
    },
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "phone": "0988888888",
      "location": "Aleppo"
    },
    "product": {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device in good condition",
      "price": 300.0,
      "condition": "used",
      "status": "available",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "category": {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone"
      },
      "images": [
        {
          "id": 90,
          "url": "https://your-domain.com/storage/products/example.jpg",
          "is_primary": false
        }
      ]
    },
    "variant": null
  },
  "message": "Order rejected successfully."
}
```

#### Error response `403`

```json
{
  "message": "You are not authorized to manage this sales order.",
  "code": "FORBIDDEN"
}
```

#### Notes

- This endpoint is seller-only and marketplace-order-only.

## Wallet API

### Domain summary

Purpose:

- Return wallet balance summary
- Return wallet ledger entries
- Allow users to submit recharge requests
- Allow users to view their recharge requests

Dependencies:

- Authentication
- Admin-side recharge approval workflow

Endpoints:

- `GET /wallet`
- `GET /wallet/transactions`
- `GET /wallet/recharge-requests`
- `POST /wallet/recharge-requests`

### Wallet summary object

```json
{
  "user_id": 5,
  "balance": 250.0,
  "transactions_count": 12,
  "recharge_requests_count": 3,
  "pending_recharge_requests_count": 1
}
```

### Wallet transaction object

```json
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
```

### Recharge request object

```json
{
  "id": 9,
  "amount": 100.0,
  "type": "deposit",
  "payment_method": "syriatel_cash",
  "status": "pending",
  "reference_number": null,
  "notes": null,
  "admin_notes": null,
  "proof_image_url": "https://your-domain.com/storage/payment_proofs/example.jpg",
  "created_at": "2026-04-13T11:00:00Z",
  "updated_at": "2026-04-13T11:00:00Z"
}
```

### GET /wallet

Return the authenticated user's wallet summary.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### GET /wallet/transactions

Return the authenticated user's wallet transactions.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### GET /wallet/recharge-requests

Return the authenticated user's recharge requests.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

```json
{
  "data": [
    {
      "id": 9,
      "amount": 100.0,
      "type": "deposit",
      "payment_method": "syriatel_cash",
      "status": "pending",
      "reference_number": null,
      "notes": null,
      "admin_notes": null,
      "proof_image_url": "https://your-domain.com/storage/payment_proofs/example.jpg",
      "created_at": "2026-04-13T11:00:00Z",
      "updated_at": "2026-04-13T11:00:00Z"
    }
  ],
  "message": "Recharge requests retrieved successfully.",
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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### POST /wallet/recharge-requests

Create a new wallet recharge request.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
  - `Content-Type: multipart/form-data` when sending `proof`

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `amount` | number | Yes | Minimum `1` |
| `method` | string | Yes | `syriatel_cash`, `mtn_cash`, or `stripe` |
| `proof` | file | No | Image only: `jpeg`, `png`, `jpg`, max `2048 KB` |

#### Example multipart fields

```text
amount=100
method=syriatel_cash
proof=<image file>
```

#### Success response `201`

```json
{
  "data": {
    "id": 9,
    "amount": 100.0,
    "type": "deposit",
    "payment_method": "syriatel_cash",
    "status": "pending",
    "reference_number": null,
    "notes": null,
    "admin_notes": null,
    "proof_image_url": "https://your-domain.com/storage/payment_proofs/example.jpg",
    "created_at": "2026-04-13T11:00:00Z",
    "updated_at": "2026-04-13T11:00:00Z"
  },
  "message": "Recharge request created successfully."
}
```

#### Error response `422`

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "proof": [
      "The proof field must be an image."
    ]
  }
}
```

#### Notes

- Creating a recharge request does **not** increase wallet balance immediately.
- Recharge requests are stored with:
  - `type = deposit`
  - `status = pending`
- Balance changes only after the admin-side approval workflow.

## Products API

### Domain summary

Purpose:

- Provide a public catalog of products
- Provide public category data

Dependencies:

- None for read access

Endpoints:

- `GET /products`
- `GET /products/{product}`
- `GET /categories`

### Product object

```json
{
  "id": 31,
  "name": "iPhone 13",
  "slug": "iphone-13-31",
  "brand": "Apple",
  "model": "13",
  "description": "Used device in good condition",
  "defects": null,
  "condition_notes": null,
  "accessories": "Charger",
  "disassembled_is": false,
  "reason_disassembly": null,
  "price": 300.0,
  "condition": "used",
  "status": "available",
  "source": "user",
  "color": "Black",
  "location": "Aleppo",
  "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
  "created_at": "2026-04-13T10:00:00Z",
  "updated_at": "2026-04-13T10:00:00Z",
  "seller": {
    "id": 8,
    "name": "Mohammad",
    "username": "seller01",
    "location": "Aleppo"
  },
  "category": {
    "id": 2,
    "name": "iPhone",
    "slug": "iphone"
  },
  "images": [
    {
      "id": 90,
      "url": "https://your-domain.com/storage/products/example.jpg",
      "is_primary": false
    }
  ],
  "variants": [
    {
      "id": 7,
      "color_name": "Black",
      "color_code": "#000000",
      "stock_quantity": 12,
      "price_modifier": 0.0
    }
  ]
}
```

### Category object

```json
{
  "id": 2,
  "name": "iPhone",
  "slug": "iphone",
  "description": "Apple iPhone devices",
  "icon": "fa-mobile-screen",
  "products_count": 14
}
```

### Known product values

- `source`: `inventory`, `user`
- `condition`: `new`, `used`
- Known listing status values in the system: `available`, `sold`, `hidden`, `pending`, `rejected`

### GET /products

Return the public product catalog.

- Authentication required: `No`
- Headers:
  - `Accept: application/json`
- Query parameters:
  - `source` optional, one of `inventory`, `user`
  - `status` optional
  - `page` optional

#### Success response `200`

```json
{
  "data": [
    {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device in good condition",
      "defects": null,
      "condition_notes": null,
      "accessories": "Charger",
      "disassembled_is": false,
      "reason_disassembly": null,
      "price": 300.0,
      "condition": "used",
      "status": "available",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "created_at": "2026-04-13T10:00:00Z",
      "updated_at": "2026-04-13T10:00:00Z",
      "seller": {
        "id": 8,
        "name": "Mohammad",
        "username": "seller01",
        "location": "Aleppo"
      },
      "category": {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone"
      },
      "images": [
        {
          "id": 90,
          "url": "https://your-domain.com/storage/products/example.jpg",
          "is_primary": false
        }
      ],
      "variants": [
        {
          "id": 7,
          "color_name": "Black",
          "color_code": "#000000",
          "stock_quantity": 12,
          "price_modifier": 0.0
        }
      ]
    }
  ],
  "message": "Products retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 12,
    "total": 1,
    "from": 1,
    "to": 1,
    "has_more_pages": false
  }
}
```

#### Error response `429`

```json
{
  "message": "Too many requests.",
  "code": "RATE_LIMIT_EXCEEDED"
}
```

#### Notes

- If `status` is omitted, empty, `null`, or `all`, the API excludes `pending` products.
- If `status` is provided with another value, the API filters by exact status.

### GET /products/{product}

Return a single product by ID.

- Authentication required: `No`
- Headers:
  - `Accept: application/json`

#### Success response `200`

```json
{
  "data": {
    "id": 31,
    "name": "iPhone 13",
    "slug": "iphone-13-31",
    "brand": "Apple",
    "model": "13",
    "description": "Used device in good condition",
    "defects": null,
    "condition_notes": null,
    "accessories": "Charger",
    "disassembled_is": false,
    "reason_disassembly": null,
    "price": 300.0,
    "condition": "used",
    "status": "available",
    "source": "user",
    "color": "Black",
    "location": "Aleppo",
    "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
    "created_at": "2026-04-13T10:00:00Z",
    "updated_at": "2026-04-13T10:00:00Z",
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "location": "Aleppo"
    },
    "category": {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone"
    },
    "images": [
      {
        "id": 90,
        "url": "https://your-domain.com/storage/products/example.jpg",
        "is_primary": false
      }
    ],
    "variants": [
      {
        "id": 7,
        "color_name": "Black",
        "color_code": "#000000",
        "stock_quantity": 12,
        "price_modifier": 0.0
      }
    ]
  },
  "message": "Product retrieved successfully."
}
```

#### Error response `404`

```json
{
  "message": "Resource not found.",
  "code": "NOT_FOUND"
}
```

#### Notes

- This endpoint loads the requested product directly by route-model binding and then loads related data.
- It does not reapply the public list filtering rules used by `GET /products`.

### GET /categories

Return all categories with product counts.

- Authentication required: `No`
- Headers:
  - `Accept: application/json`

#### Success response `200`

```json
{
  "data": [
    {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone",
      "description": "Apple iPhone devices",
      "icon": "fa-mobile-screen",
      "products_count": 14
    },
    {
      "id": 4,
      "name": "Samsung",
      "slug": "samsung",
      "description": "Samsung smartphones",
      "icon": "fa-mobile-screen",
      "products_count": 9
    }
  ],
  "message": "Categories retrieved successfully."
}
```

#### Error response `429`

```json
{
  "message": "Too many requests.",
  "code": "RATE_LIMIT_EXCEEDED"
}
```

#### Notes

- This endpoint is not paginated.

## Listings API

### Domain summary

Purpose:

- Let the authenticated user manage their own product listings

Dependencies:

- Authentication
- Category existence
- Multipart uploads for images

Endpoints:

- `GET /me/listings`
- `POST /me/listings`
- `PATCH /me/listings/{product}`
- `DELETE /me/listings/{product}`

### GET /me/listings

Return the authenticated user's listings.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

```json
{
  "data": [
    {
      "id": 31,
      "name": "iPhone 13",
      "slug": "iphone-13-31",
      "brand": "Apple",
      "model": "13",
      "description": "Used device in good condition",
      "defects": null,
      "condition_notes": null,
      "accessories": "Charger",
      "disassembled_is": false,
      "reason_disassembly": null,
      "price": 300.0,
      "condition": "used",
      "status": "pending",
      "source": "user",
      "color": "Black",
      "location": "Aleppo",
      "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
      "created_at": "2026-04-13T10:00:00Z",
      "updated_at": "2026-04-13T10:00:00Z",
      "seller": {
        "id": 8,
        "name": "Mohammad",
        "username": "seller01",
        "location": "Aleppo"
      },
      "category": {
        "id": 2,
        "name": "iPhone",
        "slug": "iphone"
      },
      "images": [
        {
          "id": 90,
          "url": "https://your-domain.com/storage/products/example.jpg",
          "is_primary": false
        }
      ],
      "variants": []
    }
  ],
  "message": "Listings retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 12,
    "total": 1,
    "from": 1,
    "to": 1,
    "has_more_pages": false
  }
}
```

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### POST /me/listings

Create a new user listing.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
  - `Content-Type: multipart/form-data`

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `brand` | string | Yes | Max `255` |
| `model` | string | Yes | Max `255` |
| `category_id` | integer | Yes | Must exist in `categories` |
| `price` | number | Yes | Minimum `0` |
| `condition` | string | Yes | `new` or `used` |
| `description` | string | No | Max `5000` |
| `condition_notes` | string | No | Max `1000` |
| `accessories` | string | No | Max `2000` |
| `disassembled_is` | boolean | No | Stored as boolean/integer flag |
| `location` | string | Yes | Max `255` |
| `color` | string | Yes | Max `50` |
| `images` | array of files | Yes | Minimum `1`, maximum `5` |
| `images[]` item | file | Yes | `jpeg`, `png`, `jpg`, `webp`, max `5120 KB` each |

#### Example multipart fields

```text
brand=Apple
model=iPhone 13
category_id=2
price=300
condition=used
description=Used device in good condition
condition_notes=Minor scratches
accessories=Charger
disassembled_is=0
location=Aleppo
color=Black
images[]=<image file 1>
images[]=<image file 2>
```

#### Success response `201`

```json
{
  "data": {
    "id": 31,
    "name": "Apple iPhone 13",
    "slug": "apple-iphone-13-6800c1d1c4f8a",
    "brand": "Apple",
    "model": "iPhone 13",
    "description": "Used device in good condition",
    "defects": null,
    "condition_notes": "Minor scratches",
    "accessories": "Charger",
    "disassembled_is": false,
    "reason_disassembly": null,
    "price": 300.0,
    "condition": "used",
    "status": "pending",
    "source": "user",
    "color": "Black",
    "location": "Aleppo",
    "primary_image_url": "https://your-domain.com/storage/products/example.jpg",
    "created_at": "2026-04-13T12:00:00Z",
    "updated_at": "2026-04-13T12:00:00Z",
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "location": "Aleppo"
    },
    "category": {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone"
    },
    "images": [
      {
        "id": 90,
        "url": "https://your-domain.com/storage/products/example.jpg",
        "is_primary": false
      }
    ],
    "variants": []
  },
  "message": "Listing created successfully."
}
```

#### Error response `422`

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "images": [
      "The images field is required."
    ]
  }
}
```

#### Notes

- New user listings are created with:
  - `source = user`
  - `status = pending`
- The backend generates `name` and `slug`.

### PATCH /me/listings/{product}

Update a user-owned listing.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
  - `Content-Type: multipart/form-data` when uploading new images

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `brand` | string | Yes | Max `255` |
| `model` | string | Yes | Max `255` |
| `category_id` | integer | Yes | Must exist in `categories` |
| `price` | number | Yes | Minimum `0` |
| `condition` | string | Yes | `new` or `used` |
| `color` | string | Yes | Max `50` |
| `status` | string | No | `available`, `sold`, `hidden`, `pending`, `rejected` |
| `description` | string | No | Max `5000` |
| `defects` | string | No | Max `2000` |
| `accessories` | string | No | Max `2000` |
| `disassembled_is` | boolean | No | Stored as boolean/integer flag |
| `delete_images` | array | No | Array of `product_images.id` values |
| `delete_images[]` item | integer | No | Must exist in `product_images` |
| `images` | array of files | No | Maximum `5` uploaded in request |
| `images[]` item | file | No | `jpeg`, `png`, `jpg`, `webp`, max `5120 KB` each |

#### Example multipart fields

```text
brand=Apple
model=iPhone 13
category_id=2
price=320
condition=used
color=Black
status=available
description=Updated description
defects=Small scratch on corner
accessories=Charger and box
disassembled_is=0
delete_images[]=90
images[]=<new image file>
```

#### Success response `200`

```json
{
  "data": {
    "id": 31,
    "name": "Apple iPhone 13",
    "slug": "apple-iphone-13-31",
    "brand": "Apple",
    "model": "iPhone 13",
    "description": "Updated description",
    "defects": "Small scratch on corner",
    "condition_notes": "Minor scratches",
    "accessories": "Charger and box",
    "disassembled_is": false,
    "reason_disassembly": null,
    "price": 320.0,
    "condition": "used",
    "status": "available",
    "source": "user",
    "color": "Black",
    "location": "Aleppo",
    "primary_image_url": "https://your-domain.com/storage/products/example-new.jpg",
    "created_at": "2026-04-13T12:00:00Z",
    "updated_at": "2026-04-13T12:30:00Z",
    "seller": {
      "id": 8,
      "name": "Mohammad",
      "username": "seller01",
      "location": "Aleppo"
    },
    "category": {
      "id": 2,
      "name": "iPhone",
      "slug": "iphone"
    },
    "images": [
      {
        "id": 91,
        "url": "https://your-domain.com/storage/products/example-new.jpg",
        "is_primary": false
      }
    ],
    "variants": []
  },
  "message": "Listing updated successfully."
}
```

#### Error response `422`

```json
{
  "message": "لا يمكن أن يتجاوز مجموع الصور 5 صور. يرجى حذف بعض الصور القديمة أولاً.",
  "code": "LISTING_IMAGE_LIMIT_EXCEEDED"
}
```

#### Notes

- Only the listing owner may update the listing.
- Unauthorized access returns `403 FORBIDDEN`.
- If the final total number of images would exceed `5`, the API returns `LISTING_IMAGE_LIMIT_EXCEEDED`.

### DELETE /me/listings/{product}

Delete a user-owned listing.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

```json
{
  "data": null,
  "message": "Listing deleted successfully."
}
```

#### Error response `403`

```json
{
  "message": "This action is unauthorized.",
  "code": "FORBIDDEN"
}
```

#### Notes

- Only the listing owner may delete the listing.

## Notifications API

### Domain summary

Purpose:

- Return the authenticated user's notifications
- Mark one notification as read
- Mark all notifications as read

Dependencies:

- Authentication

Endpoints:

- `GET /notifications`
- `POST /notifications/{id}/read`
- `POST /notifications/read-all`

### Notification object

```json
{
  "id": "database-notification-uuid",
  "title": "تم استلام طلبك",
  "message": "تم استلام طلبك #14 بنجاح.",
  "type": "order",
  "is_read": false,
  "read_at": null,
  "created_at": "2026-04-13T10:15:30Z",
  "meta": {
    "order_id": 14
  },
  "has_action": true
}
```

### GET /notifications

Return the authenticated user's notifications.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Query parameters:
  - `page` optional

#### Success response `200`

```json
{
  "data": [
    {
      "id": "database-notification-uuid",
      "title": "تم استلام طلبك",
      "message": "تم استلام طلبك #14 بنجاح.",
      "type": "order",
      "is_read": false,
      "read_at": null,
      "created_at": "2026-04-13T10:15:30Z",
      "meta": {
        "order_id": 14
      },
      "has_action": true
    }
  ],
  "message": "Notifications retrieved successfully.",
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1,
    "from": 1,
    "to": 1,
    "has_more_pages": false
  }
}
```

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

#### Notes

- The API removes Blade/web-specific `url` values from the payload.
- If a notification originally contains a URL, the API exposes only:
  - `has_action = true`
  - remaining custom data under `meta`

### POST /notifications/{id}/read

Mark one notification as read.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Request body: none

#### Success response `200`

```json
{
  "data": {
    "id": "database-notification-uuid",
    "title": "تم استلام طلبك",
    "message": "تم استلام طلبك #14 بنجاح.",
    "type": "order",
    "is_read": true,
    "read_at": "2026-04-13T10:30:00Z",
    "created_at": "2026-04-13T10:15:30Z",
    "meta": {
      "order_id": 14
    },
    "has_action": true
  },
  "message": "Notification marked as read successfully."
}
```

#### Error response `404`

```json
{
  "message": "Notification not found.",
  "code": "NOTIFICATION_NOT_FOUND"
}
```

#### Notes

- The notification must belong to the authenticated user.

### POST /notifications/read-all

Mark all unread notifications for the authenticated user as read.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
- Request body: none

#### Success response `200`

```json
{
  "data": {
    "updated_count": 4
  },
  "message": "Notifications marked as read successfully."
}
```

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

## Profile API

### Domain summary

Purpose:

- Return the authenticated user's full profile
- Update basic profile fields

Dependencies:

- Authentication

Endpoints:

- `GET /me`
- `PATCH /me`

### Profile object

```json
{
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
}
```

### GET /me

Return the authenticated user's full profile.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`

#### Success response `200`

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

#### Error response `401`

```json
{
  "message": "Unauthenticated.",
  "code": "UNAUTHENTICATED"
}
```

### PATCH /me

Update the authenticated user's profile.

- Authentication required: `Yes`
- Headers:
  - `Accept: application/json`
  - `Authorization: Bearer YOUR_TOKEN`
  - `Content-Type: application/json`

#### Request body

| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | string | Yes | Max `255` |
| `email` | string | Yes | Lowercase, valid email, unique except current user |

#### Example request

```json
{
  "name": "Ahmed Khaled",
  "email": "ahmed.khaled@example.com"
}
```

#### Success response `200`

```json
{
  "data": {
    "id": 5,
    "name": "Ahmed Khaled",
    "email": "ahmed.khaled@example.com",
    "username": "ahmed",
    "phone": "0999999999",
    "gender": null,
    "date_of_birth": null,
    "location": "Damascus",
    "status": "active",
    "role": "user",
    "wallet_balance": 250.0,
    "email_verified_at": null,
    "created_at": "2026-04-01T12:00:00Z",
    "updated_at": "2026-04-13T12:45:00Z"
  },
  "message": "Profile updated successfully."
}
```

#### Error response `422`

```json
{
  "message": "The given data was invalid.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

#### Notes

- If the email changes, `email_verified_at` is reset to `null`.
- This endpoint currently updates only `name` and `email`.

## File Upload Guide

Two API areas accept file uploads.

### 1. Wallet recharge proof

Endpoint:

- `POST /wallet/recharge-requests`

Field:

- `proof`

Rules:

- Optional
- Must be an image
- Allowed extensions: `jpeg`, `png`, `jpg`
- Maximum size: `2048 KB`

### 2. Listing images

Endpoints:

- `POST /me/listings`
- `PATCH /me/listings/{product}`

Field:

- `images[]`

Rules:

- Image only
- Allowed extensions: `jpeg`, `png`, `jpg`, `webp`
- Maximum size: `5120 KB` per image
- Create listing: `1` to `5` images required
- Update listing: optional, but total image count after update may not exceed `5`

### Mobile upload notes

- Use `multipart/form-data`
- Send each file as a separate part
- For array uploads, send repeated keys like:

```text
images[]=file1
images[]=file2
```

- Keep the bearer token header on multipart requests as well:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
Content-Type: multipart/form-data
```

## Domain Summary

| Domain | What it does | Authentication | Key endpoints |
|---|---|---|---|
| Auth | Login, current auth user, logout | Public + Sanctum | `/auth/login`, `/auth/me`, `/auth/logout` |
| Orders | Buyer orders and seller sales actions | Sanctum | `/orders`, `/orders/{id}`, `/sales/orders` |
| Wallet | Wallet summary, transactions, recharge requests | Sanctum | `/wallet`, `/wallet/transactions`, `/wallet/recharge-requests` |
| Products | Public catalog and categories | Public | `/products`, `/products/{id}`, `/categories` |
| Listings | Current user's listing management | Sanctum | `/me/listings` |
| Notifications | Notification list and read actions | Sanctum | `/notifications`, `/notifications/{id}/read`, `/notifications/read-all` |
| Profile | Current user's full profile | Sanctum | `/me` |

## Mobile Integration Guide

### Recommended flow

#### 1. Login and save token

1. Call `POST /auth/login`
2. Save `data.token`
3. Send it in every protected request:

```http
Authorization: Bearer YOUR_TOKEN
```

#### 2. Load current user

Use:

- `GET /auth/me` for auth/session confirmation
- `GET /me` for the full profile

#### 3. Fetch products

Use:

- `GET /products`
- `GET /products/{id}`
- `GET /categories`

Suggested public flow:

1. Load categories
2. Load products list
3. Filter by `source` and optionally `status`
4. Open product details by product ID

#### 4. Create an order

Use:

- `POST /orders`

Important:

- `color` is a **variant ID**, not a text color name
- Handle business error codes, especially:
  - `ORDER_PRODUCT_NOT_AVAILABLE`
  - `ORDER_WALLET_BALANCE_INSUFFICIENT`
  - `ORDER_PENDING_DUPLICATE`

#### 5. Manage seller sales orders

Use:

- `GET /sales/orders`
- `POST /sales/orders/{id}/approve`
- `POST /sales/orders/{id}/reject`

Important:

- Approval may fail until admin approval exists
- Use the stable `code` field for app logic instead of parsing the `message`

#### 6. Upload files

Use multipart for:

- `POST /wallet/recharge-requests`
- `POST /me/listings`
- `PATCH /me/listings/{id}`

#### 7. Handle pagination

For paginated endpoints:

1. Read `meta.current_page`
2. Read `meta.last_page`
3. Request the next page with `?page=2`
4. Stop when `has_more_pages` becomes `false`

### Mobile implementation warnings

- Always send `Accept: application/json`
- Always send the bearer token on protected routes
- Do not depend on human-readable `message` text for business logic
- Use the `code` field for programmatic handling
- Do not expect HTML redirects or Blade content from API routes
- Notifications do not expose web URLs; use `meta` plus `has_action` for client-side deep-link handling
- Product and proof image URLs depend on the backend storage URL configuration

## Endpoint Checklist

The current API surface includes the following endpoints:

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`
- `GET /api/v1/products`
- `GET /api/v1/products/{product}`
- `GET /api/v1/categories`
- `GET /api/v1/me`
- `PATCH /api/v1/me`
- `GET /api/v1/me/listings`
- `POST /api/v1/me/listings`
- `PATCH /api/v1/me/listings/{product}`
- `DELETE /api/v1/me/listings/{product}`
- `GET /api/v1/notifications`
- `POST /api/v1/notifications/{id}/read`
- `POST /api/v1/notifications/read-all`
- `GET /api/v1/wallet`
- `GET /api/v1/wallet/transactions`
- `GET /api/v1/wallet/recharge-requests`
- `POST /api/v1/wallet/recharge-requests`
- `GET /api/v1/orders`
- `POST /api/v1/orders`
- `GET /api/v1/orders/{order}`
- `GET /api/v1/sales/orders`
- `POST /api/v1/sales/orders/{order}/approve`
- `POST /api/v1/sales/orders/{order}/reject`
