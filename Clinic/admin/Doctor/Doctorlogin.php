<?php
session_start();

// Database connection FIX - 'hospital' database nahi 'clinic' database use karna hai
$con = mysqli_connect("localhost", "root", "", "Clinic"); // Yeh line change karo

/* AUTO LOGIN USING COOKIE */
if (!isset($_SESSION['username']) && isset($_COOKIE['doctor_login'])) {
    $_SESSION['username'] = $_COOKIE['doctor_login'];
    header("Location: indexdoc.php");
    exit();
}

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Pahle connection check karo
    if (!$con) {
        echo "<script>
                alert('Database Connection Failed!');
                window.location='Doctorlogin.php';
              </script>";
        exit();
    }

    $query = "SELECT * FROM register_doctor 
              WHERE Username='$username' 
              AND Password='$password'"; // Yeh line bhi theek hai

    $result = mysqli_query($con, $query);

    // Agar query fail hua to error show karo
    if ($result === false) {
        $error = mysqli_error($con);
        echo "<script>
                alert('Query Error: " . addslashes($error) . "');
                window.location='Doctorlogin.php';
              </script>";
        exit();
    }

    if (mysqli_num_rows($result) > 0) {

        $_SESSION['username'] = $username;

        // ✅ Remember Me Cookie
        if (isset($_POST['remember'])) {
            setcookie("doctor_login", $username, time() + (86400 * 30), "/"); 
            // 30 days
        }

        echo "<script>
                alert('Login Successful!');
                window.location='indexdoc.php';
              </script>";

    } else {
        echo "<script>
                alert('Invalid Username or Password!');
                window.location='Doctorlogin.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Doctor Login | Hospital Management System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: "Poppins", sans-serif;
        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        position: relative;
    }

    /* Animated floating circles */
    .bg-circle {
        position: absolute;
        border-radius: 50%;
        animation: float 8s infinite ease-in-out;
        filter: blur(10px);
        opacity: 0.5;
    }
    .c1 { width: 200px; height: 200px; background: #007bff; top: 10%; left: 5%; }
    .c2 { width: 260px; height: 260px; background: #6610f2; bottom: 15%; right: 8%; animation-duration: 10s; }
    .c3 { width: 150px; height: 150px; background: #00c6ff; bottom: 30%; left: 20%; animation-duration: 12s; }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-40px); }
        100% { transform: translateY(0px); }
    }

    /* Glassmorphism login card */
    .login-card {
        width: 100%;
        max-width: 440px;
        padding: 45px;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 35px rgba(0,0,0,0.3);
        text-align: center;
        animation: slideUp 1s ease forwards;
        transform: translateY(40px);
        opacity: 0;
    }

    @keyframes slideUp {
        to { transform: translateY(0); opacity: 1; }
    }

    .doctor-icon {
        font-size: 4.5rem;
        color: #00d4ff;
        text-shadow: 0 0 25px #00d4ff;
        animation: glow 2s infinite alternate;
    }

    @keyframes glow {
        from { text-shadow: 0 0 15px #00d4ff; }
        to { text-shadow: 0 0 35px #00d4ff; }
    }

    .login-card h3 {
        color: #fff;
        font-weight: 700;
        margin-top: 15px;
        text-shadow: 0 0 10px #000;
    }

    .login-card p {
        color: #ddd;
        font-size: 0.9rem;
    }

    /* Input style */
    .form-group {
        position: relative;
    }

    .form-control {
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.4);
        padding-left: 45px;
        color: white;
    }

    .form-control::placeholder { color: #ddd; }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #00eaff;
    }

    /* Login button */
    .btn-login {
        background: linear-gradient(135deg, #00eaff, #007bff);
        border: none;
        color: white;
        font-size: 18px;
        font-weight: 700;
        padding: 12px;
        border-radius: 12px;
        transition: 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,150,255,0.3);
    }

    .btn-login:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,150,255,0.5);
    }

    a { color: #00eaff !important; }

    .footer-text {
        color: #ccc;
        font-size: 0.85rem;
        margin-top: 15px;
    }

    /* Home Button Styles */
    .home-btn-container {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 1000;
    }

    .home-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.9);
        color: #0066cc !important;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        border: 2px solid #0066cc;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 102, 204, 0.15);
    }

    .home-btn:hover {
        background: #0066cc;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 102, 204, 0.25);
    }

    /* Remember me checkbox */
    .remember-me {
        text-align: left;
        margin: 15px 0;
    }
    
    .remember-me label {
        color: #ddd;
        margin-left: 5px;
    }
    
    .remember-me input[type="checkbox"] {
        accent-color: #00eaff;
    }

</style>
</head>
<body>

<!-- Floating animated background circles -->
<div class="bg-circle c1"></div>
<div class="bg-circle c2"></div>
<div class="bg-circle c3"></div>

<!-- Home Button -->
<div class="home-btn-container">
    <a href="/Clinic/Home.php" class="home-btn">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
</div>

<div class="login-card">
    <i class="fa-solid fa-user-doctor doctor-icon"></i>
    <h3>Doctor Login</h3>
    <p>Access your dashboard securely</p>

    <form action="Doctorlogin.php" method="POST">

        <div class="form-group mb-3">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <div class="form-group mb-3">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
        </div>

        <button type="submit" name="login" class="btn btn-login w-100 mt-3">
            <i class="fa-solid fa-right-to-bracket me-2"></i> Login
        </button>
    </form>

    <div class="footer-text">
        <a href="Registerdoctor.php">Register Account?</a><br>
        <i class="fa-solid fa-hospital mt-2 me-1"></i> Hospital Management © 2025
    </div>
</div>

</body>
</html>