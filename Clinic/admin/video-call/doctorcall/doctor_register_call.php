<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "clinic"); // Make sure DB name matches your database
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['register'])){
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialization = $_POST['specialization'];
    $license_number = $_POST['license_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO doctors_register_call (full_name, username, email, phone, specialization, license_number, password) 
            VALUES ('$full_name','$username','$email','$phone','$specialization','$license_number','$password')";

    if(mysqli_query($conn, $sql)){
        $_SESSION['success'] = "Registration Successful. Please login!";
        header("Location: doctor_login_call.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Doctor Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
<h2>Doctor Registration</h2>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST" action="">
<input type="text" name="full_name" placeholder="Full Name" class="form-control mb-2" required>
<input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
<input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
<input type="text" name="phone" placeholder="Phone" class="form-control mb-2">
<input type="text" name="specialization" placeholder="Specialization" class="form-control mb-2">
<input type="text" name="license_number" placeholder="License Number" class="form-control mb-2">
<input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
<button type="submit" name="register" name="register" class="btn btn-primary">Register</button>
</form>

<p class="mt-2">Already have an account? <a href="doctor_login_call.php">Login here</a></p>
</div>
</body>
</html>
