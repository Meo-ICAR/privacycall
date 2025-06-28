# Training Employee Management

This feature allows you to easily add and remove employees from trainings using a simple checkbox interface.

## How to Use

### 1. Access the Feature
- Navigate to any training detail page
- Click the "Manage Employees" button in the top-right corner
- Or click the "Manage Enrollment" button in the employee summary card

### 2. Select Employees
- You'll see a list of all employees in your company
- Check the boxes next to employees you want to enroll in the training
- Uncheck boxes to remove employees from the training
- Use "Select All" or "Deselect All" buttons for bulk operations

### 3. Save Changes
- Click "Save Changes" to apply your selections
- The system will automatically add new employees and remove unselected ones
- You'll be redirected back to the training detail page with a success message

## Features

### Checkbox Interface
- Simple checkbox selection for each employee
- Shows employee name, position, and email
- Pre-checked boxes indicate currently enrolled employees
- Real-time counter showing selected employees

### Bulk Operations
- "Select All" button to enroll all employees
- "Deselect All" button to remove all employees
- Individual checkbox selection for precise control

### Visual Feedback
- Employee summary card on training detail page
- Shows current enrollment count
- Quick access to manage enrollment
- Success/error messages after operations

### Security
- Only authenticated users can access
- Company-scoped (users can only manage their own company's employees)
- Proper validation of employee IDs

## Technical Details

### Routes
- `GET /trainings/{training}/manage-employees` - Show employee selection form
- `POST /trainings/{training}/update-employees` - Update employee assignments

### Database
- Uses the existing `employee_training` pivot table
- Leverages Laravel's `sync()` method for efficient updates
- Maintains existing pivot data (attended, completed, score, notes)

### Controller Methods
- `manageEmployees()` - Shows the form with current assignments
- `updateEmployees()` - Handles form submission and updates assignments

## Benefits

1. **Simple and Intuitive**: Checkbox interface is familiar and easy to use
2. **Efficient**: Bulk operations save time when managing many employees
3. **Visual**: Clear indication of current enrollment status
4. **Flexible**: Can add or remove employees in a single operation
5. **Secure**: Proper access controls and validation

## Example Workflow

1. Create a new training session
2. Navigate to the training detail page
3. Click "Manage Employees"
4. Check boxes for employees who should attend
5. Click "Save Changes"
6. View the updated enrollment list on the training detail page
7. Later, return to add/remove employees as needed
