<?php
session_start();

// Check login
if(!isset($_SESSION['username'])){
    header("Location: Patientlogin.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "Clinic");
if(!$con){
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Fetch doctor list
$doctor_query = "SELECT Full_Name, Email FROM register_doctor ORDER BY Full_Name ASC";
$doctor_result = mysqli_query($con, $doctor_query);
$doctors = [];
if($doctor_result){
    while($row = mysqli_fetch_assoc($doctor_result)){
        $doctors[] = $row;
    }
}

// Handle form
if(isset($_POST['submit'])){
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $appointment_date = mysqli_real_escape_string($con, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($con, $_POST['appointment_time']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    $insert = "INSERT INTO appointments 
        (fullname, username, email, phone, gender, doctor, appointment_date, appointment_time, message)
        VALUES ('$fullname', '$username', '$email', '$phone', '$gender', '$doctor', '$appointment_date', '$appointment_time', '$message')";

    if(mysqli_query($con, $insert)){
        $success = "Appointment booked successfully!";
    } else {
        $error = "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom right, #4b79a1, #283e51);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
    padding:20px;
    animation: fadeIn 1s ease;
}

/* Container */
.container {
    background:#ffffffbb;
    backdrop-filter: blur(8px);
    border-radius:20px;
    padding:40px;
    width:100%;
    max-width:680px;
    box-shadow:0 20px 40px rgba(0,0,0,0.25);
    position:relative;
    animation: slideUp 0.8s ease;
}

/* Animated header */
h2 {
    text-align:center;
    color:#222;
    margin-bottom:30px;
    font-weight:700;
    font-size:32px;
    letter-spacing:1px;
    position:relative;
}

h2::after {
    content:"";
    width:80px;
    height:4px;
    background:#2575fc;
    display:block;
    margin:10px auto 0;
    border-radius:5px;
}

/* Labels + Inputs */
label {
    font-weight:600;
    color:#333;
    margin-bottom:8px;
    display:block;
}

input, select, textarea {
    width:100%;
    padding:14px 16px;
    border-radius:12px;
    border:2px solid #ddd;
    font-size:16px;
    margin-bottom:22px;
    background:#fafafa;
    transition:all 0.25s ease;
}

input:focus, select:focus, textarea:focus {
    border-color:#2575fc;
    box-shadow:0 0 8px rgba(37,117,252,0.4);
}

/* Submit Button */
input[type="submit"] {
    background:linear-gradient(135deg,#2575fc,#6a11cb);
    color:#fff;
    border:none;
    font-size:20px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    border-radius:15px;
}

input[type="submit"]:hover {
    transform:translateY(-4px);
    box-shadow:0 10px 20px rgba(0,0,0,0.15);
}

/* Success / Error msg */
.message, .error {
    text-align:center;
    font-weight:600;
    margin-bottom:20px;
    font-size:18px;
}
.message { color:green; }
.error { color:red; }

/* Floating icon top */
.top-icon {
    font-size:60px;
    color:#fff;
    position:absolute;
    top:-35px;
    left:50%;
    transform:translateX(-50%);
    background:#2575fc;
    padding:18px 22px;
    border-radius:50%;
    box-shadow:0 10px 20px rgba(0,0,0,0.2);
}

/* Keyframes */
@keyframes slideUp {
    from { transform: translateY(40px); opacity:0; }
    to { transform: translateY(0); opacity:1; }
}

@keyframes fadeIn {
    from { opacity:0; }
    to { opacity:1; }
}

</style>
</head>
<body>

<div class="container">

    <div class="top-icon"><i class="fa-solid fa-calendar-check"></i></div>

<h2>Book Your Appointment</h2>

<?php if(isset($success)) echo "<p class='message'>$success</p>"; ?>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST" action="">
    
    <label><i class="fa-solid fa-user"></i> Full Name</label>
    <input type="text" name="fullname" placeholder="Enter your full name" required>

    <label><i class="fa-solid fa-user-tag"></i> Username</label>
    <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" readonly>

    <label><i class="fa-solid fa-envelope"></i> Email</label>
    <input type="email" name="email" placeholder="Enter your email" required>

    <label><i class="fa-solid fa-phone"></i> Phone</label>
    <input type="text" name="phone" placeholder="Enter your phone number" required>

    <label><i class="fa-solid fa-venus-mars"></i> Gender</label>
    <select name="gender" required>
        <option value="">Select</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>

    <label><i class="fa-solid fa-user-doctor"></i> Doctor</label>
    <select name="doctor" required>
        <option value="">Select Doctor</option>
        <?php foreach($doctors as $doc): ?>
        <option value="<?php echo $doc['Full_Name']; ?>">
            <?php echo $doc['Full_Name']; ?> (<?php echo $doc['Email']; ?>)
        </option>
        <?php endforeach; ?>
    </select>

    <label><i class="fa-solid fa-calendar"></i> Appointment Date</label>
    <input type="date" name="appointment_date" required>

    <label><i class="fa-solid fa-clock"></i> Appointment Time</label>
    <input type="time" name="appointment_time" required>

    <label><i class="fa-solid fa-message"></i> Message</label>
    <textarea name="message" rows="4" placeholder="Optional message"></textarea>

    <input type="submit" name="submit" value="Book Appointment">
</form>

</div>

</body>
</html>
