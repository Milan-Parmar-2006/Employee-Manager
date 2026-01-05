# Employee Management System

A simple one-page Employee Management web application built with Core PHP and MySQL. This project demonstrates basic CRUD operations without using any frameworks.

## Features

- ✅ Add new employees
- ✅ View all employees in a table
- ✅ Edit existing employee details
- ✅ Delete employees with confirmation
- ✅ Upload and display employee photos
- ✅ Secure password storage using hashing
- ✅ Automatic database and table creation

## Project Structure

```
Employees/
├── index.php          # Main application file
├── uploads/           # Directory for uploaded photos (auto-created)
│   └── [photos]       # Employee photos stored here
└── README.md          # Project documentation
```

## Requirements

- XAMPP/WAMP/LAMP server
- PHP 7.0 or higher
- MySQL database
- Web browser

## Installation & Setup

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Clone/Download Project**
   ```bash
   # Place the project in your htdocs folder
   C:\xampp\htdocs\Employees\
   ```

3. **Access Application**
   - Open browser and go to: `http://localhost/Employees/index.php`
   - Database and table will be created automatically

## Database Schema

The application automatically creates:

**Database:** `employee_management`

**Table:** `employees`
```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL
);
```

## Code Workflow

### 1. Database Connection & Setup
```php
// Connect to MySQL
$conn = mysqli_connect('localhost', 'root', '', 'employee_management');

// Create database if not exists
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS employee_management");

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS employees (...)";
```

### 2. Form Processing
```php
if ($_POST) {
    // Handle Delete
    if (isset($_POST['delete_id'])) {
        // Delete photo file and database record
    }
    
    // Handle Add/Update
    elseif (isset($_POST['submit'])) {
        // Process form data and file upload
    }
}
```

### 3. CRUD Operations

**CREATE (Add Employee)**
```php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO employees (name, email, password, mobile, photo) 
        VALUES ('$name', '$email', '$hashed_password', '$mobile', '$photo_name')";
mysqli_query($conn, $sql);
```

**READ (Display Employees)**
```php
$employees = mysqli_query($conn, "SELECT * FROM employees ORDER BY id DESC");
while ($employee = mysqli_fetch_assoc($employees)) {
    // Display employee data
}
```

**UPDATE (Edit Employee)**
```php
$sql = "UPDATE employees SET name='$name', email='$email', mobile='$mobile'";
if ($password) {
    $sql .= ", password='$hashed_password'";
}
$sql .= " WHERE id=$employee_id";
mysqli_query($conn, $sql);
```

**DELETE (Remove Employee)**
```php
// Delete photo file first
unlink('uploads/' . $photo_name);
// Delete from database
mysqli_query($conn, "DELETE FROM employees WHERE id = $id");
```

## File Upload System

### Upload Folder Usage

The `uploads/` folder is automatically created and used for:

1. **Auto-Creation**
   ```php
   if (!file_exists('uploads')) {
       mkdir('uploads');
   }
   ```

2. **File Upload Process**
   ```php
   if ($_FILES['photo']['name']) {
       $photo_name = time() . '_' . $_FILES['photo']['name'];
       move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_name);
   }
   ```

3. **File Display**
   ```php
   if ($employee['photo'] && file_exists('uploads/' . $employee['photo'])) {
       echo '<img src="uploads/' . $employee['photo'] . '" class="employee-photo">';
   }
   ```

4. **File Deletion**
   ```php
   if (file_exists('uploads/' . $photo_name)) {
       unlink('uploads/' . $photo_name);
   }
   ```

## Key Code Sections

### 1. Database Connection (Lines 2-18)
- Establishes MySQL connection
- Creates database and table automatically
- Uses basic MySQLi functions

### 2. Variables Initialization (Lines 20-24)
- Sets up variables for form handling
- Initializes edit mode variables

### 3. Form Processing (Lines 30-85)
- Handles POST requests
- Processes file uploads
- Manages CRUD operations

### 4. Edit Mode Setup (Lines 87-92)
- Fetches employee data for editing
- Populates form with existing data

### 5. HTML Form (Lines 110-170)
- Single form for both add and edit
- File upload capability
- Bootstrap styling

### 6. Employee Display (Lines 180-220)
- Table showing all employees
- Photo display with fallback
- Edit and Delete buttons

## Security Features

1. **Password Hashing**
   ```php
   $hashed_password = password_hash($password, PASSWORD_DEFAULT);
   ```

2. **File Upload Safety**
   - Unique filename using timestamp
   - File existence checks before deletion

3. **Form Validation**
   - Required field validation
   - HTML5 input types (email, tel)

## Usage Instructions

1. **Add Employee**
   - Fill the form and click "Add Employee"
   - Photo upload is optional

2. **Edit Employee**
   - Click "Edit" button next to any employee
   - Form will populate with existing data
   - Leave password blank to keep current password

3. **Delete Employee**
   - Click "Delete" button
   - Confirm deletion in popup
   - Photo file will be automatically deleted

4. **View Employees**
   - All employees displayed in table below form
   - Photos shown as thumbnails or initials

## File Structure Details

- **index.php**: Complete application in single file
- **uploads/**: Auto-created folder for employee photos
  - Photos named as: `timestamp_originalname.ext`
  - Automatically cleaned up when employee deleted

## Browser Compatibility

- Chrome, Firefox, Safari, Edge
- Responsive design using Bootstrap
- Mobile-friendly interface

## Troubleshooting

1. **Database Connection Issues**
   - Check XAMPP MySQL is running
   - Verify database credentials

2. **Photo Upload Problems**
   - Check uploads folder permissions
   - Ensure file size is reasonable

3. **Form Not Working**
   - Check PHP errors in browser console
   - Verify all required fields are filled