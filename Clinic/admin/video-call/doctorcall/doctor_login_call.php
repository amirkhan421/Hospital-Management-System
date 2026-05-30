<?php
session_start();

// 1️⃣ Connect to database
$conn = mysqli_connect("localhost", "root", "", "clinic");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2️⃣ Login logic
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch doctor with given username and password
    $query = "SELECT * FROM doctors_register_call WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $doctor = mysqli_fetch_assoc($result);

        // ✅ Set session variables
        $_SESSION['username'] = $doctor['username'];
        $_SESSION['doctor_id'] = $doctor['id'];

        // Redirect to today calls page
        header("Location: today_calls.php");
        exit();
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Doctor Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    padding:30px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
    width:400px;
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
<h3 class="text-center mb-4">Doctor Login</h3>
<?php if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="POST">
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    
    <!-- Register Link -->
    <div class="register-link">
        <p class="mt-3">Don't have an account?</p>
        <a href="doctor_register_call.php">Register as Doctor</a>
    </div>
</form>
</div>
</body>
</html>