<?php 
include_once "../db.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'email', 'username', 'password', 'department_id'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("<script>alert('Please fill all required fields.'); window.history.back();</script>");
        }
    }

    // Sanitize inputs
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = !empty($_POST['phone']) ? $conn->real_escape_string(trim($_POST['phone'])) : null;
    $department_id = intval($_POST['department_id']);
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Please enter a valid email address.'); window.history.back();</script>");
    }

    // Check if username already exists
    $check_username = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    
    if ($check_username->get_result()->num_rows > 0) {
        die("<script>alert('Username already taken. Please choose another.'); window.history.back();</script>");
    }
    $check_username->close();

    // Check if email already exists
    $check_email = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    
    if ($check_email->get_result()->num_rows > 0) {
        die("<script>alert('Email already registered. Please login instead.'); window.history.back();</script>");
    }
    $check_email->close();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into students table
        $stmt_student = $conn->prepare("INSERT INTO students (name, email, phone, department_id) VALUES (?, ?, ?, ?)");
        $stmt_student->bind_param("sssi", $name, $email, $phone, $department_id);
        
        if (!$stmt_student->execute()) {
            throw new Exception("Failed to create student record.");
        }
        
        $student_id = $stmt_student->insert_id;
        $stmt_student->close();

        // Insert into users table
        $stmt_user = $conn->prepare("INSERT INTO users (student_id, username, password, role) VALUES (?, ?, ?, 'student')");
        $stmt_user->bind_param("iss", $student_id, $username, $password);
        
        if (!$stmt_user->execute()) {
            throw new Exception("Failed to create user account.");
        }
        
        $stmt_user->close();

        $conn->commit();
        
        // Success - redirect to login
        echo "<script>
            alert('Registration successful! Welcome, $name.');
            window.location.href='login.php';
        </script>";
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Registration Error: " . $e->getMessage());
        die("<script>
            alert('Registration failed. Please try again later.');
            window.history.back();
        </script>");
    }
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4f8cff 0%, #e0e7ff 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .registration-container {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(80, 120, 255, 0.18);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(200, 200, 255, 0.18);
        }
        
        .registration-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .registration-title {
            color: #1a237e;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .registration-subtitle {
            color: #374151;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #1a237e;
            font-weight: 500;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 140, 255, 0.3);
        }
        
        .btn-register {
            width: 100%;
            padding: 13px;
            background: linear-gradient(90deg, #4f8cff 0%, #6a11cb 100%);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            background: linear-gradient(90deg, #6a11cb 0%, #4f8cff 100%);
            transform: translateY(-2px);
        }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4f8cff;
            text-decoration: none;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
        
        @media (max-width: 480px) {
            .registration-container {
                padding: 30px 20px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-header">
            <h1 class="registration-title">Student Registration</h1>
            <p class="registration-subtitle">Create your student account</p>
        </div>
        
        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="name" class="required-field">Full Name</label>
                <input type="text" id="name" name="name" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="email" class="required-field">Email Address</label>
                <input type="email" id="email" name="email" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" maxlength="20">
            </div>
            
            <div class="form-group">
                <label for="department" class="required-field">Department</label>
                <select id="department" name="department_id" required>
                    <option value="">Select Department</option>
                    <option value="1">Mathematics</option>
                    <option value="2">Computer Science</option>
                    <option value="3">Chemistry</option>
                    <option value="4">History</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="username" class="required-field">Username</label>
                <input type="text" id="username" name="username" required maxlength="50">
            </div>
            
            <div class="form-group">
                <label for="password" class="required-field">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <button type="submit" class="btn-register">Register</button>
        </form>
        
        <a href="login.php" class="login-link">Already have an account? Login here</a>
    </div>
</body>
</html>