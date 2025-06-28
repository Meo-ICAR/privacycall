# Email Integration System

## Overview

The Email Integration System allows administrators to read and reply to company emails from the `data_controller_contact` field without storing local copies. This system is designed specifically for GDPR compliance and privacy management.

## Features

### Core Functionality
- **Email Fetching**: Automatically fetch emails from external email services
- **Email Reading**: View emails with full content, attachments, and metadata
- **Email Replying**: Send replies with proper threading and formatting
- **Email Management**: Organize emails by status, priority, and category
- **GDPR Detection**: Automatically identify GDPR-related emails
- **No Local Storage**: Emails are not permanently stored locally

### Advanced Features
- **Priority Classification**: Automatic detection of urgent/high priority emails
- **Category Classification**: Categorize emails as complaints, inquiries, notifications, etc.
- **Thread Management**: Group related emails in conversations
- **Attachment Handling**: Support for multiple file attachments
- **Email Templates**: Pre-built templates for common GDPR communications
- **Statistics Dashboard**: Comprehensive email analytics
- **Search & Filtering**: Advanced search and filtering capabilities

## Architecture

### Models

#### CompanyEmail
- Stores email metadata and content temporarily
- Relationships with Company and User models
- Comprehensive scopes for filtering and querying
- Helper methods for email management

#### Company
- Contains `data_controller_contact` field for email address
- Relationship with CompanyEmail model

### Services

#### EmailIntegrationService
- Handles email fetching from external services
- Processes email content and metadata
- Manages email sending and replying
- Provides statistics and analytics

### Controllers

#### CompanyEmailController
- Full CRUD operations for email management
- RESTful API endpoints
- Web interface support
- Permission-based access control

## Installation & Setup

### 1. Database Migration
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=CompanyEmailSeeder
```

### 3. Configure Email Service
Add email service configuration to `config/services.php`:
```php
'gmail' => [
    'api_key' => env('GMAIL_API_KEY'),
    'client_id' => env('GMAIL_CLIENT_ID'),
    'client_secret' => env('GMAIL_CLIENT_SECRET'),
],
```

### 4. Environment Variables
Add to your `.env` file:
```env
GMAIL_API_KEY=your_gmail_api_key
GMAIL_CLIENT_ID=your_gmail_client_id
GMAIL_CLIENT_SECRET=your_gmail_client_secret
```

## Usage

### Web Interface

#### Access Email Management
1. Navigate to a company's show page
2. Click "Manage Emails" button (only visible if company has `data_controller_contact`)
3. Or use the "Email Management" link in the navigation menu

#### Email Dashboard
- View all emails for the company
- Filter by status, priority, category, and GDPR-related
- Search emails by content, sender, or subject
- View email statistics

#### Email Actions
- **View Email**: Click on any email to view full content
- **Reply**: Send replies to emails with proper threading
- **Mark as Read**: Automatically marks emails as read when viewed
- **Archive**: Move emails to archived status
- **Update Priority**: Change email priority level
- **Add Notes**: Add internal notes to emails
- **Delete**: Remove emails from the system

#### Send New Email
- Compose new emails to external recipients
- Use pre-built GDPR templates
- Attach files (up to 10MB per file)
- Track email status and delivery

### Command Line Interface

#### Fetch Emails for All Companies
```bash
php artisan emails:fetch --all
```

#### Fetch Emails for Specific Company
```bash
php artisan emails:fetch --company-id=1
```

### API Endpoints

#### Email Management
```
GET    /companies/{company}/emails              # List emails
GET    /companies/{company}/emails/create       # Create email form
POST   /companies/{company}/emails              # Send new email
GET    /companies/{company}/emails/{email}      # Show email
GET    /companies/{company}/emails/{email}/reply # Reply form
POST   /companies/{company}/emails/{email}/reply # Send reply
PUT    /companies/{company}/emails/{email}      # Update email
DELETE /companies/{company}/emails/{email}      # Delete email
```

#### Email Operations
```
POST   /companies/{company}/emails/fetch        # Fetch new emails
GET    /companies/{company}/emails/stats        # Get statistics
```

## Email Classification

### GDPR Detection
The system automatically detects GDPR-related emails based on keywords:
- gdpr, data protection, privacy, consent
- data subject, right to be forgotten, data portability
- data breach, personal data, processing
- data protection officer, dpo, artificial intelligence
- ai, machine learning, automated decision making

### Priority Classification
- **Urgent**: Contains keywords like "urgent", "immediate", "asap", "emergency", "critical"
- **High**: Contains keywords like "important", "priority", "attention", "deadline"
- **Normal**: Default priority level

### Category Classification
- **Complaint**: Contains "complaint" or "grievance"
- **Inquiry**: Contains "request", "inquiry", or "question"
- **Notification**: Contains "notification", "update", or "inform"
- **General**: Default category

## Email Templates

### Available Templates
1. **GDPR Compliance Inquiry**: Request for compliance information
2. **Data Subject Request**: Handle data subject rights requests
3. **Data Breach Notification**: Notify about data breaches
4. **Consent Management Update**: Update consent preferences
5. **Privacy Policy Update**: Inform about policy changes

### Template Usage
1. Go to "Send New Email" page
2. Select a template from the dropdown
3. Template content will automatically populate
4. Customize the content as needed
5. Send the email

## Security & Permissions

### Access Control
- Only users with `admin` or `superadmin` roles can access email management
- Users can only access emails for their own company
- Company isolation ensures data privacy

### Data Protection
- Emails are not permanently stored locally
- Temporary storage for processing and display
- Automatic cleanup of old email data
- Encrypted storage of sensitive information

## Testing

### Run Tests
```bash
php artisan test --filter=CompanyEmailTest
```

### Test Coverage
- Email CRUD operations
- Permission and access control
- Email classification and filtering
- Service functionality
- Model relationships and scopes

## Configuration

### Email Service Configuration
The system supports multiple email service providers:
- Gmail API (default)
- IMAP/POP3
- Microsoft Graph API
- Custom email providers

### Storage Configuration
- Email attachments stored in `storage/app/public/email_attachments/`
- Temporary email data in database
- Configurable retention periods

### Performance Settings
- Configurable batch sizes for email fetching
- Rate limiting for external API calls
- Caching for frequently accessed data

## Troubleshooting

### Common Issues

#### Email Fetching Fails
1. Check email service credentials
2. Verify company has `data_controller_contact` set
3. Check network connectivity
4. Review error logs

#### Permission Denied
1. Ensure user has admin/superadmin role
2. Verify user belongs to the company
3. Check company access permissions

#### Email Not Displaying
1. Check email status (unread/read/replied)
2. Verify email belongs to correct company
3. Check database connectivity

### Debug Commands
```bash
# Check email service status
php artisan emails:fetch --company-id=1

# View email statistics
php artisan tinker
>>> $company = App\Models\Company::first();
>>> app(App\Services\EmailIntegrationService::class)->getEmailStats($company);
```

## Future Enhancements

### Planned Features
- Real-time email notifications
- Advanced email analytics
- Integration with more email providers
- Automated email responses
- Email workflow automation
- Advanced GDPR compliance features

### API Enhancements
- Webhook support for real-time updates
- GraphQL API for complex queries
- Bulk email operations
- Advanced filtering and search

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

## License

This email integration system is part of the PrivacyCall application and follows the same licensing terms.
