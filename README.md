# PrivacyCall - GDPR Compliant Company Management System

A comprehensive Laravel-based system for managing companies, employees, customers, and suppliers with full GDPR compliance and data protection features.

## 🛡️ GDPR Compliance Features

### Data Subject Rights
- **Right to be Forgotten**: Complete data deletion upon request
- **Right to Data Portability**: Export personal data in multiple formats (JSON, CSV, XML)
- **Right to Access**: View all personal data and processing activities
- **Right to Rectification**: Update and correct personal information
- **Right to Restrict Processing**: Limit data processing activities
- **Right to Object**: Object to data processing

### Consent Management
- **Granular Consent**: Separate consent for different processing purposes
- **Consent History**: Complete audit trail of consent changes
- **Consent Withdrawal**: Easy consent withdrawal process
- **Consent Expiry**: Automatic consent expiration tracking
- **Consent Evidence**: Document consent with screenshots, documents, or recordings

### Data Processing Activities
- **Processing Register**: Comprehensive record of all data processing activities
- **Legal Basis Tracking**: Document legal basis for each processing activity
- **Risk Assessment**: Risk level assessment for processing activities
- **Data Protection Impact Assessment (DPIA)**: Support for DPIA requirements
- **Third Country Transfers**: Track international data transfers

## 🏢 Company Management Features

### Company Types
- **Employers**: Companies that employ staff
- **Customers**: Companies that purchase products/services
- **Suppliers**: Companies that provide products/services
- **Partners**: Strategic business partners

### Company Information
- Legal and trading names
- Registration and VAT numbers
- Complete address information
- Contact details and website
- Industry classification and company size
- GDPR compliance information

## 👥 Employee Management

### Employee Records
- Personal information (name, email, phone)
- Employment details (position, department, hire date)
- Salary and employment type
- Emergency contact information
- GDPR consent tracking

### Employment Types
- Full-time
- Part-time
- Contract
- Temporary

## 🛒 Customer Management

### Customer Records
- Personal/business information
- Contact preferences
- Purchase history
- Customer status tracking
- GDPR consent management

### Customer Types
- Individual customers
- Business customers

## 🚚 Supplier Management

### Supplier Records
- Company information
- Contact person details
- Supply categories and status
- Financial information
- GDPR compliance tracking

### Supplier Categories
- Primary suppliers
- Secondary suppliers
- Emergency suppliers

## 🗄️ Database Structure

### Core Tables
- `companies` - Company information and GDPR compliance
- `employees` - Employee records with consent tracking
- `customers` - Customer records with privacy preferences
- `suppliers` - Supplier records with compliance data
- `users` - User accounts with role-based access

### GDPR Tables
- `data_processing_activities` - Processing activity register
- `consent_records` - Consent history and evidence

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Laravel 12.x

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/Meo-ICAR/privacycall.git
   cd privacycall
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=privacycall
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📡 API Endpoints

### Company Management
```
GET    /api/v1/companies              - List companies
POST   /api/v1/companies              - Create company
GET    /api/v1/companies/{id}         - Get company details
PUT    /api/v1/companies/{id}         - Update company
DELETE /api/v1/companies/{id}         - Delete company
GET    /api/v1/companies/{id}/gdpr-status - Get GDPR status
```

### GDPR Data Subject Rights
```
POST   /api/v1/gdpr/right-to-be-forgotten    - Request data deletion
POST   /api/v1/gdpr/data-portability         - Request data export
POST   /api/v1/gdpr/export-data              - Export data in format
GET    /api/v1/gdpr/data-processing-activities - Get processing activities
GET    /api/v1/gdpr/consent-history          - Get consent history
```

## 🔐 Security Features

### Data Protection
- **Encryption**: Sensitive data encryption at rest
- **Access Control**: Role-based access control
- **Audit Logging**: Complete audit trail of data access
- **Data Minimization**: Only collect necessary data
- **Purpose Limitation**: Clear processing purposes

### Privacy by Design
- **Default Privacy**: Privacy-friendly default settings
- **Privacy Settings**: Granular privacy controls
- **Data Retention**: Automatic data retention policies
- **Data Anonymization**: Support for data anonymization

## 📊 GDPR Compliance Dashboard

The system includes comprehensive GDPR compliance monitoring:

### Compliance Metrics
- Consent validity status
- Data retention compliance
- Processing activity tracking
- Data subject rights requests
- Breach notification tracking

### Reporting Features
- GDPR compliance reports
- Data processing activity reports
- Consent management reports
- Data subject rights reports

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

### GDPR Compliance Tests
```bash
php artisan test --filter=GdprTest
```

## 📝 Documentation

### API Documentation
- Complete API documentation available at `/api/documentation`
- OpenAPI/Swagger specification
- Request/response examples

### GDPR Documentation
- GDPR compliance guide
- Data processing procedures
- Consent management procedures
- Data subject rights procedures

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure GDPR compliance
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Contact the development team
- Check the documentation

## 🔄 Version History

### v1.0.0 (Current)
- Initial GDPR-compliant company management system
- Complete data subject rights implementation
- Consent management system
- Data processing activity tracking
- API endpoints for all major functions

## 🏛️ Legal Compliance

This system is designed to help organizations comply with:
- **GDPR (General Data Protection Regulation)**
- **CCPA (California Consumer Privacy Act)**
- **LGPD (Brazilian General Data Protection Law)**
- **Other international privacy laws**

## ⚠️ Disclaimer

This software is provided as-is for educational and development purposes. Organizations should:
- Conduct their own legal review
- Implement appropriate security measures
- Train staff on GDPR requirements
- Regularly audit compliance
- Consult with legal professionals

---

**Built with ❤️ for privacy and data protection**
