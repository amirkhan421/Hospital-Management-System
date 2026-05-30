<?php
session_start();
$con = mysqli_connect("localhost","root","","clinic");

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // FIXED: Username aur password dono check karein
    $query = "SELECT * FROM patient_register_call WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0){
         $_SESSION['username'] = $username;
         $_SESSION['password'] = $password;
        header("Location: patient_choose&profile.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Username or Password');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Login Call</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background: linear-gradient(135deg,#1565c0,#0d47a1);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
/* Home Button Styles */
.home-btn-container {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
}

.home-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #0d47a1 !important;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    border: 2px solid #0d47a1;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(13, 71, 161, 0.15);
}

.home-btn:hover {
    background: #0d47a1;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 71, 161, 0.25);
    text-decoration: none;
}
.login-box{
    background:white;
    padding:40px;
    border-radius:20px;
    width:100%;
    max-width:400px;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
}
.login-box h2{
    text-align:center;
    margin-bottom:25px;
    color:#0d47a1;
}
.register-link{
    text-align:center;
    margin-top:15px;
}
.register-link a{
    text-decoration:none;
    font-weight:600;
    color:#0d47a1;
}
.register-link a:hover{
    text-decoration:underline;
}
</style>
</head>

<body>

<!-- Home Button -->
<div class="home-btn-container">
    <a href="../Choose_page_login_option.php" class="home-btn">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
</div>


<div class="login-box">
    <h2>Patient Login Call</h2>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" >
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>
    
    <div class="register-link">
        <p>Don't have an account?</p>
        <a href="patient_register_call.php">Create New Account</a>
    </div>
</div>

</body>
</html>

