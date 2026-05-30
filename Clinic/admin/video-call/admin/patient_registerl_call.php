<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = mysqli_connect("localhost", "root", "", "clinic");

// Check connection
if (mysqli_connect_errno()) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$error = '';

if(isset($_POST['send'])) {
    
    // Get and sanitize form data
    $full_name   = mysqli_real_escape_string($con, trim($_POST['full_name']));
    $username    = mysqli_real_escape_string($con, trim($_POST['username']));
    $email       = mysqli_real_escape_string($con, trim($_POST['email']));
    $phone       = mysqli_real_escape_string($con, trim($_POST['phone']));
    $gender      = mysqli_real_escape_string($con, trim($_POST['gender']));
    $age         = intval($_POST['age']);
    $blood_group = mysqli_real_escape_string($con, trim($_POST['blood_group']));
    $address     = mysqli_real_escape_string($con, trim($_POST['address']));
    $password    = $_POST['password'];
    
    // Basic validation
    if(empty($full_name) || empty($username) || empty($email) || empty($phone) || 
       empty($gender) || empty($age) || empty($blood_group) || empty($address) || empty($password)) {
        $error = "All fields are required!";
    }
    elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if username or email already exists
        $check_sql = "SELECT id FROM patient_register_call WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($con, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error = "Username or email already exists!";
        } else {
            // IMPORTANT: Using created_at column (as per your database), NOT registration_date
            $insert_sql = "INSERT INTO patient_register_call 
                (full_name, username, email, phone, gender, age, blood_group, address, password, created_at) 
                VALUES 
                ('$full_name', '$username', '$email', '$phone', '$gender', $age, '$blood_group', '$address', '$hashed_password', NOW())";
            
            if(mysqli_query($con, $insert_sql)) {
                $_SESSION['registration_success'] = true;
                $_SESSION['success_message'] = "Registration successful! Please login.";
                header("Location: patient_login_call.php");
                exit();
            } else {
                $error = "Registration failed: " . mysqli_error($con);
            }
        }
    }
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - AWCC Clinic</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1a237e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .registration-container {
            width: 100%;
            max-width: 500px;
        }
        
        .registration-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        
        .registration-header {
            background: linear-gradient(135deg, #2196F3, #0d47a1);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        
        .registration-header h3 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .registration-body {
            padding: 30px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 2;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
            background: white;
        }
        
        .form-control:focus + .form-icon {
            color: #2196F3;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            z-index: 3;
        }
        
        .password-toggle:hover {
            color: #2196F3;
        }
        
        .error-message {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #2196F3, #0d47a1);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(33, 150, 243, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-link a {
            color: #2196F3;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .login-link a:hover {
            color: #0d47a1;
        }
        
        .row {
            display: flex;
            gap: 15px;
        }
        
        .row > div {
            flex: 1;
        }
        
        @media (max-width: 576px) {
            .row {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    
    <div class="registration-container">
        <div class="registration-card">
            <div class="registration-header">
                <i class="fas fa-user-plus header-icon"></i>
                <h3>Patient Registration</h3>
                <p>Join AWCC Clinic for online consultations</p>
            </div>
            
            <div class="registration-body">
                <?php if(!empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="registrationForm">
                    <div class="form-group">
                        <i class="fas fa-user form-icon"></i>
                        <input type="text" 
                               name="full_name" 
                               class="form-control" 
                               placeholder="Full Name"
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-at form-icon"></i>
                        <input type="text" 
                               name="username" 
                               class="form-control" 
                               placeholder="Username"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-envelope form-icon"></i>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Email"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-phone form-icon"></i>
                        <input type="tel" 
                               name="phone" 
                               class="form-control" 
                               placeholder="Phone Number"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <i class="fas fa-venus-mars form-icon"></i>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <i class="fas fa-birthday-cake form-icon"></i>
                                <input type="number" 
                                       name="age" 
                                       class="form-control" 
                                       placeholder="Age"
                                       min="1" 
                                       max="120"
                                       value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-tint form-icon"></i>
                        <select name="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-home form-icon"></i>
                        <textarea name="address" 
                                  class="form-control" 
                                  placeholder="Address"
                                  rows="3"
                                  required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock form-icon"></i>
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control" 
                               placeholder="Password (min 6 characters)"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <button type="submit" name="send" class="btn-register" id="registerButton">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>
                </form>
                
                <div class="login-link">
                    <p>
                        <a href="patient_login_call.php">
                            <i class="fas fa-sign-in-alt"></i>
                            Already have an account? Login here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            password.type = password.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
        
        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                document.getElementById('password').focus();
                return;
            }
        });
        
        // Phone validation
        document.querySelector('input[name="phone"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-]/g, '');
        });
        
        // Age validation
        document.querySelector('input[name="age"]').addEventListener('input', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 120) this.value = 120;
        });
    </script>
</body>
</html>
