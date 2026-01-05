<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'employee_management');

// Create database if not exists
if (!$conn) {
    $conn = mysqli_connect('localhost', 'root', '');
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS employee_management");
    mysqli_close($conn);
    $conn = mysqli_connect('localhost', 'root', '', 'employee_management');
}

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL
)";
mysqli_query($conn, $table_sql);

// Variables
$message = '';
$edit_id = 0;
$edit_data = null;

// Create uploads folder
if (!file_exists('uploads')) {
    mkdir('uploads');
}

// Handle form submission
if ($_POST) {
    
    // Delete employee
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        
        // Get photo name to delete file
        $result = mysqli_query($conn, "SELECT photo FROM employees WHERE id = $id");
        $row = mysqli_fetch_assoc($result);
        if ($row['photo'] && file_exists('uploads/' . $row['photo'])) {
            unlink('uploads/' . $row['photo']);
        }
        
        // Delete from database
        mysqli_query($conn, "DELETE FROM employees WHERE id = $id");
        $message = "Employee deleted successfully!";
    }
    
    // Add or Update employee
    elseif (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $mobile = $_POST['mobile'];
        $employee_id = $_POST['employee_id'];
        
        // Handle photo upload
        $photo_name = '';
        if ($_FILES['photo']['name']) {
            $photo_name = time() . '_' . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo_name);
        }
        
        if ($employee_id) {
            // Update employee
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE employees SET name='$name', email='$email', mobile='$mobile'";
            
            if ($password) {
                $sql .= ", password='$hashed_password'";
            }
            if ($photo_name) {
                $sql .= ", photo='$photo_name'";
            }
            $sql .= " WHERE id=$employee_id";
            
            mysqli_query($conn, $sql);
            $message = "Employee updated successfully!";
        } else {
            // Add new employee
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO employees (name, email, password, mobile, photo) 
                    VALUES ('$name', '$email', '$hashed_password', '$mobile', '$photo_name')";
            mysqli_query($conn, $sql);
            $message = "Employee added successfully!";
        }
    }
}

// Get employee for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM employees WHERE id = $edit_id");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all employees
$employees = mysqli_query($conn, "SELECT * FROM employees ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .employee-photo { width: 50px; height: 50px; border-radius: 50%; }
        .form-container { background: #f8f9fa; padding: 20px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Employee Management System</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Employee Form -->
        <div class="form-container">
            <h3><?php echo $edit_data ? 'Edit Employee' : 'Add New Employee'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="employee_id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?php echo $edit_data ? $edit_data['name'] : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required 
                               value="<?php echo $edit_data ? $edit_data['email'] : ''; ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <?php echo $edit_data ? '(leave blank to keep current)' : '*'; ?></label>
                        <input type="password" name="password" class="form-control" <?php echo !$edit_data ? 'required' : ''; ?>>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" name="mobile" class="form-control" required 
                               value="<?php echo $edit_data ? $edit_data['mobile'] : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <?php if ($edit_data && $edit_data['photo']): ?>
                        <small class="text-muted">Current: <?php echo $edit_data['photo']; ?></small>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary">
                    <?php echo $edit_data ? 'Update Employee' : 'Add Employee'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Employees Table -->
        <div class="card">
            <div class="card-header">
                <h4>All Employees</h4>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($employees) == 0): ?>
                    <p>No employees found.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($employee = mysqli_fetch_assoc($employees)): ?>
                                <tr>
                                    <td>
                                        <?php if ($employee['photo'] && file_exists('uploads/' . $employee['photo'])): ?>
                                            <img src="uploads/<?php echo $employee['photo']; ?>" class="employee-photo">
                                        <?php else: ?>
                                            <div class="employee-photo bg-secondary text-white text-center">
                                                <?php echo strtoupper(substr($employee['name'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $employee['name']; ?></td>
                                    <td><?php echo $employee['email']; ?></td>
                                    <td><?php echo $employee['mobile']; ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $employee['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                            <input type="hidden" name="delete_id" value="<?php echo $employee['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>