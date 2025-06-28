# Representative API Documentation

This document describes the API endpoints for managing representatives and their disclosure subscriptions.

## Overview

The Representative API allows you to manage company representatives who are responsible for handling various types of disclosures and compliance matters. Each representative is associated with a company and can subscribe to different types of disclosure notifications.

## Base URL

```
/api/v1/representatives
```

## Data Model

### Representative Fields

- `company_id` (required): ID of the associated company
- `first_name` (required): Representative's first name
- `last_name` (required): Representative's last name
- `email` (required, unique): Representative's email address
- `phone` (optional): Representative's phone number
- `position` (optional): Job position/title
- `department` (optional): Department within the company
- `disclosure_subscriptions` (optional): Array of disclosure types the representative is subscribed to
- `last_disclosure_date` (optional): Date of the last disclosure sent
- `is_active` (boolean): Whether the representative is active (default: true)
- `notes` (optional): Additional notes about the representative
- `email_notifications` (boolean): Whether to send email notifications (default: true)
- `sms_notifications` (boolean): Whether to send SMS notifications (default: false)
- `preferred_contact_method` (string): Preferred contact method - 'email', 'phone', or 'sms' (default: 'email')

### Disclosure Types

Common disclosure types include:
- `gdpr_updates`
- `data_breach_notifications`
- `privacy_policy_changes`
- `consent_management`
- `security_updates`
- `employee_data_processing`
- `third_party_disclosures`
- `data_retention_changes`

## API Endpoints

### 1. List Representatives

**GET** `/api/v1/representatives`

**Query Parameters:**
- `company_id` (optional): Filter by company ID
- `is_active` (optional): Filter by active status (true/false)
- `search` (optional): Search by name or email
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "company_id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "email": "john.doe@company.com",
                "position": "Data Protection Officer",
                "department": "Legal",
                "is_active": true,
                "company": {
                    "id": 1,
                    "name": "Sample Company Ltd"
                }
            }
        ],
        "current_page": 1,
        "total": 1
    },
    "message": "Representatives retrieved successfully"
}
```

### 2. Create Representative

**POST** `/api/v1/representatives`

**Request Body:**
```json
{
    "company_id": 1,
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@company.com",
    "phone": "+1234567890",
    "position": "Privacy Manager",
    "department": "Compliance",
    "disclosure_subscriptions": ["gdpr_updates", "data_breach_notifications"],
    "is_active": true,
    "email_notifications": true,
    "sms_notifications": false,
    "preferred_contact_method": "email",
    "notes": "Primary contact for privacy matters"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "company_id": 1,
        "first_name": "Jane",
        "last_name": "Smith",
        "email": "jane.smith@company.com",
        "company": {
            "id": 1,
            "name": "Sample Company Ltd"
        }
    },
    "message": "Representative created successfully"
}
```

### 3. Get Representative

**GET** `/api/v1/representatives/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "company_id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@company.com",
        "disclosure_subscriptions": ["gdpr_updates", "data_breach_notifications"],
        "is_active": true,
        "company": {
            "id": 1,
            "name": "Sample Company Ltd"
        }
    },
    "disclosure_summary": {
        "total_subscriptions": 2,
        "subscription_types": ["gdpr_updates", "data_breach_notifications"],
        "last_disclosure_date": "2024-01-15 10:30:00",
        "days_since_last_disclosure": 5
    },
    "message": "Representative retrieved successfully"
}
```

### 4. Update Representative

**PUT** `/api/v1/representatives/{id}`

**Request Body:**
```json
{
    "first_name": "John Updated",
    "position": "Senior Data Protection Officer",
    "is_active": false
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "first_name": "John Updated",
        "position": "Senior Data Protection Officer",
        "is_active": false
    },
    "message": "Representative updated successfully"
}
```

### 5. Delete Representative

**DELETE** `/api/v1/representatives/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Representative deleted successfully"
}
```

### 6. Add Disclosure Subscription

**POST** `/api/v1/representatives/{id}/add-disclosure-subscription`

**Request Body:**
```json
{
    "disclosure_type": "privacy_policy_changes"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "disclosure_subscriptions": ["gdpr_updates", "data_breach_notifications", "privacy_policy_changes"]
    },
    "disclosure_summary": {
        "total_subscriptions": 3,
        "subscription_types": ["gdpr_updates", "data_breach_notifications", "privacy_policy_changes"]
    },
    "message": "Disclosure subscription added successfully"
}
```

### 7. Remove Disclosure Subscription

**POST** `/api/v1/representatives/{id}/remove-disclosure-subscription`

**Request Body:**
```json
{
    "disclosure_type": "privacy_policy_changes"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "disclosure_subscriptions": ["gdpr_updates", "data_breach_notifications"]
    },
    "disclosure_summary": {
        "total_subscriptions": 2,
        "subscription_types": ["gdpr_updates", "data_breach_notifications"]
    },
    "message": "Disclosure subscription removed successfully"
}
```

### 8. Update Last Disclosure Date

**POST** `/api/v1/representatives/{id}/update-last-disclosure-date`

**Response:**
```json
{
    "success": true,
    "data": {
        "last_disclosure_date": "2024-01-20 14:30:00"
    },
    "disclosure_summary": {
        "last_disclosure_date": "2024-01-20 14:30:00",
        "days_since_last_disclosure": 0
    },
    "message": "Last disclosure date updated successfully"
}
```

### 9. Get Representatives by Company

**GET** `/api/v1/representatives/company/{company_id}`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@company.com",
            "is_active": true
        },
        {
            "id": 2,
            "first_name": "Jane",
            "last_name": "Smith",
            "email": "jane.smith@company.com",
            "is_active": true
        }
    ],
    "message": "Company representatives retrieved successfully"
}
```

### 10. Get Disclosure Summary

**GET** `/api/v1/representatives/disclosure-summary`

**Query Parameters:**
- `company_id` (optional): Filter by company ID

**Response:**
```json
{
    "success": true,
    "data": {
        "total_representatives": 5,
        "active_representatives": 4,
        "total_subscriptions": 12,
        "subscription_types": ["gdpr_updates", "data_breach_notifications", "privacy_policy_changes"],
        "representatives_with_subscriptions": 4
    },
    "message": "Disclosure summary retrieved successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "company_id": ["The selected company id is invalid."]
    }
}
```

### Not Found Error (404)
```json
{
    "message": "No query results for model [App\\Models\\Representative] 999"
}
```

### Business Logic Error (400)
```json
{
    "success": false,
    "message": "Representative is already subscribed to this disclosure type"
}
```

## Usage Examples

### Creating a Representative with Disclosure Subscriptions
```bash
curl -X POST http://localhost:8000/api/v1/representatives \
  -H "Content-Type: application/json" \
  -d '{
    "company_id": 1,
    "first_name": "Alice",
    "last_name": "Johnson",
    "email": "alice.johnson@company.com",
    "position": "Compliance Officer",
    "disclosure_subscriptions": ["gdpr_updates", "consent_management"],
    "email_notifications": true,
    "preferred_contact_method": "email"
  }'
```

### Adding a Disclosure Subscription
```bash
curl -X POST http://localhost:8000/api/v1/representatives/1/add-disclosure-subscription \
  -H "Content-Type: application/json" \
  -d '{
    "disclosure_type": "data_breach_notifications"
  }'
```

### Getting Representatives for a Specific Company
```bash
curl -X GET "http://localhost:8000/api/v1/representatives?company_id=1&is_active=true"
```

## Testing

Run the test suite to verify all functionality:

```bash
php artisan test --filter=RepresentativeTest
```

## Database Migration

The representatives table has been created with the following structure:

- Foreign key relationship with companies table
- JSON field for disclosure subscriptions
- Soft deletes for data retention
- Indexes for performance optimization
- Timestamps for audit trails
