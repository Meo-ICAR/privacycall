# Representative Cloning and Logo Features

This document describes the new features added to the Representative management system: logo uploads and cloning functionality for superadmins.

## Logo Feature

### Overview
Representatives can now have profile photos/logos uploaded and displayed throughout the system.

### Features
- **Logo Upload**: Representatives can upload profile photos in JPEG, PNG, GIF, or SVG format (max 2MB)
- **Default Avatar**: If no logo is uploaded, a default avatar is generated using the representative's initials
- **Logo Display**: Logos are displayed in all representative views (index, show, forms)
- **Logo Preview**: Real-time preview when uploading logos in create/edit forms
- **Logo Storage**: Logos are stored in the `storage/app/public/representative_logos/` directory

### Database Changes
- Added `logo_url` field to `representatives` table
- Field stores the URL path to the uploaded logo file

### Usage
1. **Upload Logo**: Use the logo upload field in create/edit forms
2. **View Logo**: Logos are automatically displayed in representative cards and detail views
3. **Replace Logo**: Upload a new logo to replace the existing one (old file is automatically deleted)

## Cloning Feature

### Overview
Superadmins can clone representatives across different companies while maintaining a reference to the original record.

### Features
- **Single Clone**: Clone a representative to one target company
- **Multiple Clone**: Clone a representative to multiple companies at once
- **Original Tracking**: Each cloned representative maintains a reference to the original record
- **Email Uniqueness**: Automatic email suffix generation to ensure uniqueness
- **Field Overrides**: Ability to modify specific fields during cloning
- **Clone Detection**: Visual indicators show which representatives are clones

### Database Changes
- Added `original_id` field to `representatives` table (foreign key to self)
- Added index on `original_id` for performance
- Foreign key constraint with `nullOnDelete()` to handle original deletion

### Model Methods
```php
// Check if representative is a clone
$representative->isClone();

// Check if representative has clones
$representative->hasClones();

// Get the original representative
$representative->original;

// Get all clones of this representative
$representative->clones;

// Get root representative (original or self)
$representative->getRootRepresentative();

// Clone to another company
$cloned = $representative->cloneToCompany($targetCompanyId, $overrides);

// Get all related representatives (original + clones)
$related = $representative->getAllRelated();
```

### Scopes
```php
// Get only original representatives (not clones)
Representative::originals()->get();

// Get only cloned representatives
Representative::clones()->get();
```

### Usage

#### Single Clone
1. Navigate to a representative's detail page
2. Click the "Clone" button (superadmin only)
3. Select target company
4. Optionally modify fields
5. Submit to create the clone

#### Multiple Clone (API)
```bash
POST /representatives/{id}/clone-multiple
{
    "target_company_ids": [1, 2, 3],
    "overrides": {
        "first_name": "Modified Name",
        "notes": "Cloned representative"
    }
}
```

### Clone Information Display
- **Clone Badge**: Yellow badge with copy icon on cloned representatives
- **Original Reference**: Shows "Cloned from: [Original Name]" in company column
- **Related Representatives**: Sidebar shows original and all clones
- **Clone Alert**: Warning message on cloned representative detail pages

## Access Control

### Logo Feature
- **Upload**: Available to all users who can create/edit representatives
- **View**: Available to all users who can view representatives

### Cloning Feature
- **Clone Button**: Only visible to superadmins
- **Clone Form**: Only accessible to superadmins
- **Clone API**: Only accessible to superadmins
- **Clone Information**: Visible to all users for transparency

## File Storage

### Logo Storage
- **Directory**: `storage/app/public/representative_logos/`
- **Naming**: `logo_[unique_id].[extension]`
- **URL**: `/storage/representative_logos/[filename]`
- **Cleanup**: Old logos are automatically deleted when replaced

### Storage Setup
Ensure the storage link is created:
```bash
php artisan storage:link
```

## API Endpoints

### Cloning Endpoints
```
GET    /representatives/{id}/clone                    # Show clone form
POST   /representatives/{id}/clone                    # Clone to single company
POST   /representatives/{id}/clone-multiple           # Clone to multiple companies
GET    /representatives/{id}/clones                   # Get all clones
GET    /representatives/{id}/related                  # Get all related representatives
```

### Response Examples

#### Successful Clone
```json
{
    "success": true,
    "data": {
        "id": 2,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@newcompany.com",
        "original_id": 1,
        "company": {
            "id": 2,
            "name": "New Company Ltd"
        }
    },
    "message": "Representative cloned successfully"
}
```

#### Multiple Clone
```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "email": "john.doe@company1.com",
            "company_id": 1
        },
        {
            "id": 3,
            "email": "john.doe@company2.com",
            "company_id": 2
        }
    ],
    "message": "Representative cloned to 2 companies successfully"
}
```

## Error Handling

### Logo Upload Errors
- **File Size**: Maximum 2MB limit
- **File Type**: Only JPEG, PNG, GIF, SVG allowed
- **Storage**: Automatic fallback to default avatar if upload fails

### Cloning Errors
- **Permission**: 403 error for non-superadmins
- **Company**: 422 error for invalid target company
- **Email**: Automatic suffix generation for duplicate emails
- **Validation**: Standard Laravel validation errors

## Testing

### Manual Testing
1. Create a representative with logo
2. Clone to another company
3. Verify clone information is displayed
4. Test logo upload and preview
5. Verify original-clone relationships

### Automated Testing
```bash
# Run representative tests
php artisan test --filter=RepresentativeTest

# Test cloning functionality
php artisan test --filter=RepresentativeCloningTest
```

## Migration and Rollback

### Upgrading
```bash
php artisan migrate
```

### Rolling Back
```bash
php artisan migrate:rollback --step=1
```

## Performance Considerations

### Database
- Index on `original_id` for efficient clone queries
- Soft deletes maintain referential integrity

### Storage
- Logo files are stored in public directory for direct access
- Automatic cleanup prevents storage bloat

### Caching
- Consider caching representative logos for better performance
- Implement logo resizing for different display sizes

## Future Enhancements

### Potential Features
- **Bulk Cloning**: Clone multiple representatives at once
- **Clone Templates**: Predefined override configurations
- **Logo Cropping**: In-browser image editing
- **Logo Variants**: Different sizes for different contexts
- **Clone History**: Track when and why representatives were cloned
- **Clone Synchronization**: Keep certain fields in sync between original and clones
