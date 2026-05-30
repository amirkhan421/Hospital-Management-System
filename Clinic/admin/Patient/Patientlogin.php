<?php
session_start();

if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $con = mysqli_connect('localhost','root','','Clinic');
    
    // Check connection
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $select = "SELECT * FROM patient_register_login 
               WHERE Username='$username' AND Password='$password'";

    $query = mysqli_query($con, $select);
    
    // Check if query executed successfully
    if ($query === false) {
        die("Query failed: " . mysqli_error($con));
    }

    if(mysqli_num_rows($query) > 0)
    {
        // CORRECTED: Session variables set BEFORE using them
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password; // Optional: Not recommended to store password in session
        
        header("Location: indexpat.php");
        exit();
    }
    else
    {
        $error = "Invalid username or password";
        // Show error in JavaScript alert
        echo "<script>alert('$error');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal | Hospital Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #00a859;
            --accent-color: #ff6b35;
            --dark-blue: #004085;
            --light-gray: #f8f9fa;
            --text-dark: #333333;
            --text-light: #666666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        /* Home Button Styles */
        .home-btn-container {
            position: absolute;
            top: 25px;
            left: 80px;
            z-index: 1000;
        }

        .home-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color) !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            border: 2px solid var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.15);
        }

        .home-btn:hover {
            background: var(--primary-color);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.25);
        }

        .home-btn:active {
            transform: translateY(0);
        }

        .hospital-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            min-height: 700px;
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0, 102, 204, 0.15);
        }

        /* Left Panel - Hospital Info */
        .hospital-sidebar {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-blue));
            color: white;
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
        }

        .hospital-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="white" opacity="0.05"/></svg>');
            pointer-events: none;
        }

        .hospital-logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .hospital-logo i {
            font-size: 2.5rem;
            margin-right: 15px;
        }

        .hospital-logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .hospital-tagline {
            margin-top: 30px;
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 30px;
        }

        .hospital-features {
            margin-top: 60px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .feature-item i {
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        /* QR Code Section */
        .qr-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 40px;
            text-align: center;
        }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        /* Right Panel - Login Form */
        .login-panel {
            flex: 1.2;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--light-gray);
        }

        .login-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
            outline: none;
        }

        /* Remember Me & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
            accent-color: var(--primary-color);
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--dark-blue);
            text-decoration: underline;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 204, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Language Selector */
        .language-selector {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .language-selector select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 5px 10px;
            background: white;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        /* Mobile Provider Info */
        .mobile-provider {
            position: absolute;
            bottom: 30px;
            left: 40px;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Error Message */
        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            border-left: 4px solid #c00;
        }

        .error-message.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .hospital-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .hospital-sidebar {
                padding: 30px;
            }
            
            .login-panel {
                padding: 40px;
            }
            
            .hospital-tagline {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .hospital-container {
                border-radius: 20px;
            }
            
            .hospital-sidebar,
            .login-panel {
                padding: 30px 25px;
            }
            
            .login-header h2 {
                font-size: 1.6rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Home Button -->
    <div class="home-btn-container">
        <a href="/Clinic/Home.php" class="home-btn">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
    </div>
    <div class="hospital-container">
        
        <!-- Left Sidebar - Hospital Information -->
        <div class="hospital-sidebar">
            <div class="hospital-logo">
                <i class="fas fa-hospital"></i>
                <h1>HOSPITAL<br>Management Service</h1>
            </div>
            
            <h2 class="hospital-tagline">Your Health, Our Priority</h2>
            
            <div class="hospital-features">
                <div class="feature-item">
                    <i class="fas fa-stethoscope"></i>
                    <span>Expert Medical Consultation</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Easy Appointment Booking</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-file-medical"></i>
                    <span>Digital Health Records</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>24/7 Emergency Services</span>
                </div>
            </div>
            
            <div class="qr-section">
                <div class="qr-placeholder">
                    <i class="fas fa-qrcode"></i>
                </div>
                <p>Scan QR for Mobile App</p>
                <small>Download our hospital app</small>
            </div>
            
            <div class="mobile-provider">
                <i class="fas fa-mobile-alt"></i> Mobile Provider
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="login-panel">
            <div class="language-selector">
                <i class="fas fa-globe"></i>
                <select id="languageSelect">
                    <option value="en" selected>English</option>
                    <option value="ur">اردو</option>
                </select>
            </div>
            
            <div class="login-header">
                <h2>Patient Portal</h2>
                <p>Access your medical records and appointments</p>
            </div>
            
            <div id="errorMessage" class="error-message"></div>
            


            <!-- Form  -->


            
            <form action="Patientlogin.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               name="username" 
                               class="form-control" 
                               placeholder="Enter your username"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your password"
                               required>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-password">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>
                
                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to Portal
                </button>
            </form>
            
            <div class="register-link">
                Don't have an account?
                <a href="PatientRegistration.php">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
            </div>
        </div>
    </div>
</body>
</html>