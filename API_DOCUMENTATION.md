# AICMS RESTful API Documentation

## Overview

The AICMS RESTful API provides a comprehensive interface for client applications to interact with the Companion Management System. All API endpoints are versioned and follow REST principles.

**Base URL**: `http://your-domain.com/api/v1`

**Authentication**: JWT (JSON Web Tokens)

**Content Type**: `application/json`

## Authentication

### Login

Authenticate and receive a JWT token.

**Endpoint**: `POST /auth/login`

**Request Body**:
```json
{
  "username": "your_username",
  "password": "your_password"
}
```

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "You have successfully logged in",
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "username": "johndoe",
      "email": "john@example.com"
    }
  }
}
```

### Using the Token

Include the JWT token in the Authorization header for all protected endpoints:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## Members Management

### List All Members

Get a paginated list of all members with optional filtering.

**Endpoint**: `GET /members`

**Query Parameters**:
- `per_page` (optional): Number of results per page (default: 15)
- `search` (optional): Search by surname, otherNames, or coopId
- `group_id` (optional): Filter by group ID

**Example Request**:
```bash
GET /api/v1/members?per_page=20&search=John&group_id=1
Authorization: Bearer {token}
```

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Members retrieved successfully",
  "data": {
    "data": [
      {
        "id": "uuid-here",
        "coopId": "12345",
        "groupId": 123,
        "surname": "Doe",
        "otherNames": "John",
        "occupation": "Engineer",
        "gender": "Male",
        "religion": "Christianity",
        "phoneNumber": "1234567890",
        "bankName": "First Bank",
        "accountNumber": "0123456789",
        "nextOfKinName": "Jane Doe",
        "nextOfKinPhoneNumber": "0987654321",
        "yearJoined": 2020,
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00"
      }
    ],
    "pagination": {
      "total": 100,
      "count": 20,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 5
    }
  }
}
```

### Create Member

Create a new member in the system.

**Endpoint**: `POST /members`

**Request Body**:
```json
{
  "coopId": "12345",
  "surname": "Doe",
  "otherNames": "John",
  "occupation": "Engineer",
  "gender": "Male",
  "religion": "Christianity",
  "phoneNumber": "1234567890",
  "bankName": "First Bank",
  "accountNumber": "0123456789",
  "nextOfKinName": "Jane Doe",
  "nextOfKinPhoneNumber": "0987654321",
  "yearJoined": 2020
}
```

**Response** (Success - 201):
```json
{
  "status": "success",
  "message": "Member created successfully",
  "data": {
    "id": "uuid-here",
    "coopId": "12345",
    "surname": "Doe",
    ...
  }
}
```

**Validation Errors** (422):
```json
{
  "status": "error",
  "message": "Validation error",
  "errors": {
    "coopId": ["The coop id has already been taken."],
    "surname": ["The surname field is required."]
  }
}
```

### Get Member by ID

Retrieve a specific member by their ID.

**Endpoint**: `GET /members/{id}`

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Member retrieved successfully",
  "data": {
    "id": "uuid-here",
    "coopId": "12345",
    ...
  }
}
```

### Get Member by CoopId

Retrieve a specific member by their coopId.

**Endpoint**: `GET /members/coop/{coopId}`

**Response**: Same as Get Member by ID

### Update Member

Update an existing member's information.

**Endpoint**: `PUT /members/{id}`

**Request Body** (partial update allowed):
```json
{
  "phoneNumber": "9876543210",
  "occupation": "Senior Engineer"
}
```

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Member updated successfully",
  "data": {
    ...
  }
}
```

### Delete Member

Remove a member from the system.

**Endpoint**: `DELETE /members/{id}`

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Member deleted successfully"
}
```

---

## Loans Management

### List All Loans

Get a paginated list of loans with optional filtering.

**Endpoint**: `GET /loans`

**Query Parameters**:
- `per_page` (optional): Results per page (default: 25)
- `search` (optional): Search by coopId
- `status` (optional): Filter by status (0 or 1)

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Loans retrieved successfully",
  "data": [
    {
      "id": "uuid-here",
      "coopId": "12345",
      "loanAmount": 50000,
      "loanDate": "2024-01-01",
      "repaymentDate": "2025-06-25",
      "guarantor1": "123",
      "guarantor2": "456",
      "guarantor3": null,
      "guarantor4": null,
      "status": 1,
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "total": 50,
    "last_page": 2
  }
}
```

### Create Loan

Create a new loan for a member.

**Endpoint**: `POST /loans`

**Request Body**:
```json
{
  "coopId": "12345",
  "loanAmount": 50000,
  "loanDate": "2024-01-01",
  "guarantor1": "123",
  "guarantor2": "456",
  "guarantor3": "789",
  "guarantor4": "101"
}
```

**Business Rules**:
- Member must exist
- Member cannot have an existing active loan
- Guarantors must be valid member coopIds
- Repayment date is automatically calculated (loan date + 540 days)

**Response** (Success - 201):
```json
{
  "status": "success",
  "message": "Loan created successfully",
  "data": {
    ...
  }
}
```

**Error Response** (422):
```json
{
  "status": "error",
  "message": "Member already has an active loan"
}
```

### Get Loan by ID

**Endpoint**: `GET /loans/{id}`

### Update Loan

**Endpoint**: `PUT /loans/{id}`

**Request Body** (partial update):
```json
{
  "loanAmount": 75000
}
```

### Delete Loan

**Endpoint**: `DELETE /loans/{id}`

### Mark Loan as Completed

Mark a loan as paid off and move to completed loans.

**Endpoint**: `POST /loans/{id}/complete`

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Loan marked as completed successfully",
  "data": {
    ...
  }
}
```

### Get Active Loans

Get all currently active loans.

**Endpoint**: `GET /active-loans`

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Active loans retrieved successfully",
  "data": [
    {
      "id": 1,
      "coopId": "12345",
      "loanAmount": 50000,
      "loanPaid": 10000,
      "loanBalance": 40000,
      "loanDate": "2024-01-01",
      "repaymentDate": "2025-06-25",
      "lastPaymentDate": "2024-03-01",
      "member": {
        "id": "uuid",
        "surname": "Doe",
        "otherNames": "John"
      }
    }
  ]
}
```

---

## Payments Management

### List All Payments

Get a paginated list of payments with optional filtering.

**Endpoint**: `GET /payments`

**Query Parameters**:
- `per_page` (optional): Results per page (default: 25)
- `coop_id` (optional): Filter by member coopId
- `start_date` (optional): Filter by start date (Y-m-d)
- `end_date` (optional): Filter by end date (Y-m-d)

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Payments retrieved successfully",
  "data": [
    {
      "id": "uuid",
      "coopId": "12345",
      "loanAmount": 5000,
      "savingAmount": 3000,
      "shareAmount": 2000,
      "others": 0,
      "adminCharge": 100,
      "totalAmount": 10100,
      "paymentDate": "2024-01-15",
      "splitOption": null,
      "otherSavingsType": null,
      "created_at": "2024-01-15 10:00:00",
      "updated_at": "2024-01-15 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "total": 100,
    "last_page": 4
  }
}
```

### Create Payment

Record a new payment from a member.

**Endpoint**: `POST /payments`

**Request Body**:
```json
{
  "coopId": "12345",
  "loanAmount": 5000,
  "savingAmount": 3000,
  "shareAmount": 2000,
  "others": 0,
  "adminCharge": 100,
  "paymentDate": "2024-01-15",
  "splitOption": "monthly",
  "otherSavingsType": "special"
}
```

**Notes**:
- All amount fields are optional but at least one should be provided
- If `loanAmount` is provided, member must have an active loan
- Total amount is calculated automatically
- Active loan balance is updated automatically

**Response** (Success - 201):
```json
{
  "status": "success",
  "message": "Payment created successfully",
  "data": {
    ...
  }
}
```

### Get Payment by ID

**Endpoint**: `GET /payments/{id}`

### Update Payment

**Endpoint**: `PUT /payments/{id}`

### Delete Payment

**Endpoint**: `DELETE /payments/{id}`

**Note**: If payment included loan repayment, the active loan balance will be adjusted accordingly.

---

## Annual Fees Management

### List Annual Fees

**Endpoint**: `GET /annual-fees`

**Query Parameters**:
- `per_page` (optional): Results per page (default: 25)
- `coop_id` (optional): Filter by coopId
- `year` (optional): Filter by year

### Create Annual Fee

**Endpoint**: `POST /annual-fees`

**Request Body**:
```json
{
  "coopId": "12345",
  "annual_savings": 12000,
  "annual_fee": 1000,
  "annual_year": 2024
}
```

### Get Annual Fee by ID

**Endpoint**: `GET /annual-fees/{id}`

### Update Annual Fee

**Endpoint**: `PUT /annual-fees/{id}`

### Delete Annual Fee

**Endpoint**: `DELETE /annual-fees/{id}`

---

## Admin Management

### List Admins

**Endpoint**: `GET /admins`

**Query Parameters**:
- `per_page` (optional): Results per page (default: 15)
- `search` (optional): Search by name, username, or email

### Create Admin

**Endpoint**: `POST /admins`

**Request Body**:
```json
{
  "name": "Admin User",
  "username": "adminuser",
  "email": "admin@example.com",
  "password": "securepassword",
  "coopId": "12345",
  "role": "admin"
}
```

**Roles**: `superadmin`, `admin`, `user`

### Get Admin by ID

**Endpoint**: `GET /admins/{id}`

### Update Admin

**Endpoint**: `PUT /admins/{id}`

### Delete Admin

**Endpoint**: `DELETE /admins/{id}`

---

## Business Items Management

### List Categories

**Endpoint**: `GET /categories`

**Query Parameters**:
- `per_page` (optional): Results per page
- `search` (optional): Search by name

### Create Category

**Endpoint**: `POST /categories`

**Request Body**:
```json
{
  "name": "Electronics"
}
```

### Get Category by ID

**Endpoint**: `GET /categories/{id}`

### Update Category

**Endpoint**: `PUT /categories/{id}`

### Delete Category

**Endpoint**: `DELETE /categories/{id}`

**Note**: Cannot delete category if it has items

---

### List Items

**Endpoint**: `GET /items`

**Query Parameters**:
- `per_page` (optional): Results per page (default: 25)
- `coop_id` (optional): Filter by member coopId
- `category_id` (optional): Filter by category
- `payment_status` (optional): Filter by payment status (0 or 1)

### Create Item

**Endpoint**: `POST /items`

**Request Body**:
```json
{
  "coopId": "12345",
  "category_id": 1,
  "quantity": 2,
  "price": 25000,
  "description": "Item description",
  "buyingDate": "2024-01-01",
  "payment_timeframe": 180
}
```

**Notes**:
- `payment_timeframe`: Number of days for payment
- Repayment date calculated automatically
- `loanBalance` calculated as price × quantity

### Get Item by ID

**Endpoint**: `GET /items/{id}`

### Update Item

**Endpoint**: `PUT /items/{id}`

### Delete Item

**Endpoint**: `DELETE /items/{id}`

---

## Account/Profile Endpoints

### Get User Profile

Get authenticated user's profile.

**Endpoint**: `GET /account/profile`

**Response** (Success - 200):
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "username": "johndoe",
      "email": "john@example.com",
      "coopId": "12345"
    }
  }
}
```

### Get Account Balance

Get member's account balance summary.

**Endpoint**: `GET /account/balance`

**Response** (Success - 200):
```json
{
  "savings": 150000,
  "shares": 75000,
  "loan": -40000,
  "total_balance": 185000
}
```

### Get Savings Records

Get member's savings transaction history.

**Endpoint**: `GET /account/savings`

**Response** (Success - 200):
```json
{
  "status": "success",
  "message": "Savings records fetched successfully",
  "data": [
    {
      "id": "uuid",
      "paymentDate": "2024-01-15",
      "savingsAmount": 5000
    }
  ]
}
```

### Get Shares Records

Get member's shares transaction history.

**Endpoint**: `GET /account/shares`

---

## Error Responses

### 401 Unauthorized
```json
{
  "status": "error",
  "message": "Unauthenticated"
}
```

### 404 Not Found
```json
{
  "status": "error",
  "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "status": "error",
  "message": "Validation error",
  "errors": {
    "field_name": [
      "Error message"
    ]
  }
}
```

### 500 Internal Server Error
```json
{
  "status": "error",
  "message": "Error message describing what went wrong"
}
```

---

## Rate Limiting

API requests are rate-limited to prevent abuse. Standard limits apply per IP address.

## Pagination

All list endpoints support pagination with the following parameters:
- `per_page`: Number of items per page (max 100)
- `page`: Page number (starts at 1)

Pagination information is returned in the response metadata.

## Filtering and Searching

Many endpoints support filtering and searching via query parameters. Check individual endpoint documentation for available filters.

---

## Example Usage

### JavaScript (Fetch API)

```javascript
// Login
const loginResponse = await fetch('http://your-domain.com/api/v1/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    username: 'johndoe',
    password: 'password123'
  })
});

const { access_token } = await loginResponse.json();

// Get members
const membersResponse = await fetch('http://your-domain.com/api/v1/members', {
  headers: {
    'Authorization': `Bearer ${access_token}`,
    'Content-Type': 'application/json',
  }
});

const members = await membersResponse.json();
```

### Python (Requests)

```python
import requests

# Login
login_response = requests.post(
    'http://your-domain.com/api/v1/auth/login',
    json={'username': 'johndoe', 'password': 'password123'}
)
token = login_response.json()['access_token']

# Get members
headers = {'Authorization': f'Bearer {token}'}
members_response = requests.get(
    'http://your-domain.com/api/v1/members',
    headers=headers
)
members = members_response.json()
```

### cURL

```bash
# Login
curl -X POST http://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"johndoe","password":"password123"}'

# Get members (replace TOKEN with actual token)
curl -X GET http://your-domain.com/api/v1/members \
  -H "Authorization: Bearer TOKEN"
```

---

## Support

For issues or questions about the API, please contact the development team or create an issue in the repository.
