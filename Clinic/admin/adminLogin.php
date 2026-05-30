<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "Clinic");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin_login WHERE Username='$username' AND Password='$password'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['username'] = $username;
        echo "<script>
                alert('Login Successful!');
                window.location='Selectoption.php';
              </script>";
    } else {
        echo "<script>
                alert('Invalid Username or Password!');
                window.location='adminLogin.php';
              </script>";
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Animated Admin Login | Clinic</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* Body and Background */
body {
    height: 100vh;
    margin: 0;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    animation: bgAnim 15s infinite alternate;
}
@keyframes bgAnim {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
/* ZABARDAST Home Button */
.home-btn-container {
    position: fixed;
    top: 25px;
    left: 25px;
    z-index: 1000;
}

.home-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 25px;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    color: #0d6efd !important;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    border: 3px solid #0d6efd;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25);
    animation: pulse 2s infinite;
}

.home-btn:hover {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: white !important;
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 10px 30px rgba(13, 110, 253, 0.5);
    text-decoration: none;
}

@keyframes pulse {
    0% { box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25); }
    50% { box-shadow: 0 6px 25px rgba(13, 110, 253, 0.4); }
    100% { box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25); }
}

/* Floating circles */
body::before, body::after {
content: "";
position: absolute;
border-radius: 50%;
opacity: 0.3;
animation: float 10s ease-in-out infinite;
}
body::before {
width: 300px;
height: 300px;
background: #00c6ff;
top: -100px;
left: -100px;
}
body::after {
width: 200px;
height: 200px;
background: #6610f2;
bottom: -50px;
right: -50px;
animation-duration: 14s;
}
@keyframes float {
0% { transform: translate(0,0); }
50% { transform: translate(30px,-30px); }
100% { transform: translate(0,0); }
}

/* Login Card */
.login-card {
position: relative;
background: rgba(255,255,255,0.1);
backdrop-filter: blur(20px);
border-radius: 20px;
padding: 50px 35px;
width: 100%;
max-width: 400px;
box-shadow: 0 15px 40px rgba(0,0,0,0.6);
z-index: 10;
animation: cardFade 1s ease forwards;
}
@keyframes cardFade {
from { opacity: 0; transform: translateY(-30px); }
to { opacity: 1; transform: translateY(0); }
}

/* Card Icon & Glow */
.login-card i.admin-icon {
font-size: 3.5rem;
color: #00f0ff;
display: block;
margin: 0 auto 15px;
animation: glow 2s ease-in-out infinite alternate;
}
@keyframes glow {
0% { text-shadow: 0 0 5px #00c6ff; }
100% { text-shadow: 0 0 20px #00f0ff, 0 0 30px #00c6ff; }
}

/* Headings & text */
.login-card h3 {
text-align: center;
font-weight: 700;
color: #fff;
margin-bottom: 10px;
}
.login-card p {
text-align: center;
font-size: 0.9rem;
color: #ccc;
margin-bottom: 25px;
}

/* Form inputs */
.form-control {
border-radius: 10px;
padding-left: 40px;
background: rgba(255,255,255,0.15);
border: none;
color: #fff;
margin-bottom: 20px;
transition: all 0.3s ease;
}
.form-control::placeholder {
color: #ccc;
}
.form-control:focus {
outline: none;
box-shadow: 0 0 15px rgba(0, 198, 255, 0.6);
background: rgba(255,255,255,0.25);
}

.input-group-text {
background: transparent;
border: none;
position: absolute;
left: 12px;
top: 10px;
color: #00c6ff;
}

/* Button */
.btn-login {
width: 100%;
border-radius: 10px;
background: linear-gradient(90deg, #00c6ff, #007bff);
color: white;
font-weight: 600;
border: none;
padding: 12px;
font-size: 1rem;
transition: all 0.3s ease;
}
.btn-login:hover {
background: linear-gradient(90deg, #007bff, #00c6ff);
transform: scale(1.05);
box-shadow: 0 0 20px rgba(0,198,255,0.7);
}

/* Bottom links */
.bottom-text {
text-align: center;
margin-top: 15px;
color: #aaa;
}
.bottom-text a {
color: #00c6ff;
text-decoration: none;
}
.bottom-text a:hover {
text-decoration: underline;
} </style>

</head>
<body>
<!-- Home Button -->
    <div class="home-btn-container">
        <a href="/Clinic/Home.php" class="home-btn">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
    </div>
<div class="login-card">
    <i class="fa-solid fa-user-lock admin-icon"></i>
    <h3>Login Here!!!</h3>
    <p>Secure access to your dashboard</p>
    <form action="adminLogin.php" method="POST">
        <div class="position-relative mb-3">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="position-relative mb-3">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn btn-login"><i class="fa-solid fa-right-to-bracket me-2"></i> Login</button>
        
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
