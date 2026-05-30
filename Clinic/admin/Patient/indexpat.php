<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: patientlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>

<!-- Bootstrap CDN -->
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<!-- Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        background: linear-gradient(to right, #dfe9f3, #ffffff);
        font-family: 'Segoe UI', sans-serif;
    }

    h2 {
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 1px 1px 2px #aaa;
    }

    .card {
        border-radius: 20px;
        padding: 30px;
        background: white;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0px 10px 25px rgba(0,0,0,0.25);
    }

    /* Decorative background circles */
    .card::before {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(0,0,0,0.05);
        position: absolute;
        top: -50px;
        right: -50px;
        border-radius: 50%;
    }

    .icon-box {
        width: 75px;
        height: 75px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        font-size: 30px;
    }

    .card p {
        color: #555;
        font-size: 15px;
        margin: 10px 0 20px;
    }

    /* Card Colors */
    .appoint-card { border-top: 6px solid #007bff; }
    .presc-card { border-top: 6px solid #28a745; }
    .profile-card { border-top: 6px solid #343a40; }
</style>

<!-- Extra CSS for heavy animated cards -->
<style>
    .big-section {
        background: #fff;
        padding: 40px;
        border-radius: 18px;
        box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 50px;
    }

    .info-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0px 5px 15px rgba(0,0,0,0.15);
        transition: 0.3s;
        position: relative;
    }

    .info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 25px rgba(0,0,0,0.25);
    }

    .icon-adv {
        font-size: 40px;
        color: #007bff;
        margin-bottom: 15px;
    }

    .info-big {
        background: #f8f9fa;
        border-radius: 18px;
        box-shadow: 0px 4px 14px rgba(0,0,0,0.12);
        transition: 0.3s ease;
    }

    .info-big:hover {
        transform: scale(1.03);
        box-shadow: 0px 8px 22px rgba(0,0,0,0.2);
    }

    .info-big h4 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    
    
    
    
</style>

</head>
<body>

    <a class="nav-item btn btn-danger btn-sm" href="Logoutpat.php" style="margin-left: 50px;margin-top: 10px;">
        <i class="fas fa-sign-out-alt me-1"></i> Logout
    </a>

<div class="container mt-5">

    <h2 class="text-center mb-5">Welcome, 
        <span class="text-primary">
        <?php echo $_SESSION['username']; ?>
        </span>
    </h2>

    <div class="row g-4">

        <!-- Appointment Card -->
        <div class="col-md-4">
            <div class="card appoint-card text-center">
                <div class="icon-box">
                    <i class="fas fa-calendar-check text-primary"></i>
                </div>
                <h4 class="text-primary">Patient Appointment</h4>
                <p>Book appointments, check schedules, and view your previous appointment history.</p>
                <a href="PatientAppoint.php" class="btn btn-primary w-100">Go to Appointments</a>
            </div>
        </div>

        <!-- Patient Profile Card -->
        <div class="col-md-4">
            <div class="card profile-card text-center">
                <div class="icon-box">
                    <i class="fas fa-user text-dark"></i>
                </div>
                <h4 class="text-dark">Patient Profile</h4>
                <p>See your personal information, update profile details, and manage your account.</p>
                <a href="patientpro.php" class="btn btn-dark w-100">Open Profile</a>
            </div>
        </div>

    </div>
</div>
<!-- EXTRA HEAVY SECTION BELOW (Advantages + Animated Cards) -->
<div class="big-section mt-5">

    <h3 class="text-center mb-4" style="font-weight:700;">Why Use Patient Dashboard?</h3>

    <p class="text-center mb-5" style="font-size:17px; color:#555;">
        This dashboard provides all essential patient healthcare services in one place. 
        Manage appointments, prescriptions, and your medical profile with ease.
    </p>

    <div class="row g-4">

        <!-- Advantage Card 1 -->
        <div class="col-md-4">
            <div class="info-card p-4 text-center">
                <i class="fas fa-clock icon-adv"></i>
                <h5>Quick Appointments</h5>
                <p>Schedule appointments instantly without any waiting or paperwork.</p>
            </div>
        </div>

        <!-- Advantage Card 2 -->
        <div class="col-md-4">
            <div class="info-card p-4 text-center">
                <i class="fas fa-shield-heart icon-adv"></i>
                <h5>Best Medical Support</h5>
                <p>Access trusted doctors, prescriptions, and treatment options anytime.</p>
            </div>
        </div>

        <!-- Advantage Card 3 -->
        <div class="col-md-4">
            <div class="info-card p-4 text-center">
                <i class="fas fa-hospital-user icon-adv"></i>
                <h5>Personalized Experience</h5>
                <p>All your medical records and profile information in one safe place.</p>
            </div>
        </div>

    </div>


    <!-- 2nd Row of Heavy Cards -->
    <div class="row mt-4 g-4">

        <div class="col-md-6">
            <div class="info-big p-4">
                <h4><i class="fas fa-hand-holding-medical"></i> Easy Access</h4>
                <p>This system helps you access your medical data quickly without visiting hospital desks.</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-big p-4">
                <h4><i class="fas fa-user-lock"></i> Secure Information</h4>
                <p>Your profile, appointments, and prescriptions are secure and only you can access them.</p>
            </div>
        </div>

    </div>

</div>




</body>
</html>
