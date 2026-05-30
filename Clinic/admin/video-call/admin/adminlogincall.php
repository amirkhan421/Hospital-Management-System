<?php
session_start();

// اگر پہلے سے لاگ ان ہے تو اگلے صفحے پر redirect
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: choosepageloginoption.php");
    exit();
}

$error = '';
$username = '';

// فارم submit ہونے پر
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // بنیادی validation
    if(empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Database connection
        $host = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "clinic";
        
        $conn = new mysqli($host, $db_user, $db_pass, $db_name);
        
        if($conn->connect_error) {
            $error = "Database connection failed";
        } else {
            // SQL query
            $stmt = $conn->prepare("SELECT * FROM `admin_login_call` WHERE `username` = ? AND `status` = 'active'");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                
                // Password check
                if($password === $admin['password']) {
                    // Session میں ڈیٹا save کریں
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_full_name'] = $admin['full_name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['admin_email'] = $admin['email'];
                    
                    // Last login update
                    $update_stmt = $conn->prepare("UPDATE `admin_login_call` SET `last_login` = NOW() WHERE `id` = ?");
                    $update_stmt->bind_param("i", $admin['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    // Correct redirect to select_login_type.php
                    header("Location: choosepageloginoption.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "Username not found or account inactive";
            }
            
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AWCC Clinic</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-header {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 50px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
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
            border-color: #0d47a1;
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1);
            background: white;
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
        
        .btn-login {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: white;
            border: none;
            padding: 14px;
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
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-footer p {
            color: #666;
            font-size: 14px;
        }
        
        .forgot-link {
            color: #0d47a1;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .forgot-link:hover {
            color: #1565c0;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }
            
            .login-body {
                padding: 20px;
            }
            
            .login-header {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-shield"></i>
                <h1>AWCC Clinic</h1>
                <p>Administrator Login Portal</p>
            </div>
            
            <div class="login-body">
                <?php if(!empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="loginForm">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                        <div class="input-group">
                            <i class="fas fa-at"></i>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control" 
                                   placeholder="Enter your username"
                                   value="<?php echo htmlspecialchars($username); ?>"
                                   required
                                   autocomplete="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                        <div class="input-group">
                            <i class="fas fa-key"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-login" id="loginButton">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login to Dashboard</span>
                        </button>
                    </div>
                </form>
                
                <div class="login-footer">
                    <p>
                        <a href="#" class="forgot-link">
                            <i class="fas fa-question-circle me-1"></i>Forgot Password?
                        </a>
                    </p>
                    <p style="margin-top: 5px; font-size: 12px; color: #888;">
                        &copy; <?php echo date('Y'); ?> AWCC Clinic. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password show/hide toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
        
        // Form validation and loading animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const loginButton = document.getElementById('loginButton');
            
            if (username === '' || password === '') {
                e.preventDefault();
                if (username === '') {
                    alert('Please enter your username');
                    document.getElementById('username').focus();
                } else {
                    alert('Please enter your password');
                    document.getElementById('password').focus();
                }
            } else {
                // Show loading state
                loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Logging in...</span>';
                loginButton.disabled = true;
            }
        });
        
        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
            
            // Test credentials button (for development only)
            const testBtn = document.createElement('button');
            testBtn.innerHTML = '<i class="fas fa-vial"></i> Test Credentials';
            testBtn.style.cssText = 'position:fixed; bottom:20px; right:20px; padding:10px 15px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer; font-size:12px; z-index:1000;';
            testBtn.onclick = function() {
                document.getElementById('username').value = 'khazan';
                document.getElementById('password').value = '12345';
                alert('Test credentials filled!\nUsername: khazan\nPassword: 12345');
            };
            document.body.appendChild(testBtn);
        });
    </script>
</body>
</html>