<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Animated Doctor Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: bgMove 25s ease-in-out infinite alternate;
}

/* Background gradient animation */
@keyframes bgMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container{
    z-index: 10;
}

/* Page title */
.dashboard-title{
    font-weight: 700;
    letter-spacing: 1px;
    color: #fff;
    animation: fadeInDown 1s ease forwards;
    text-shadow: 0 0 10px rgba(0,0,0,0.3);
}

/* Card hover animation */
.card-hover{
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    cursor: pointer;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border-radius: 15px;
    border: none;
    overflow: hidden;
    animation: fadeInUp 1s forwards, float 4s ease-in-out infinite;
}

/* Staggered fade-in delay */
.row .col-md-4:nth-child(1), .row .col-md-6:nth-child(4){ animation-delay: 0.2s; }
.row .col-md-4:nth-child(2), .row .col-md-6:nth-child(5){ animation-delay: 0.4s; }
.row .col-md-4:nth-child(3){ animation-delay: 0.6s; }

/* Floating effect */
@keyframes float {
    0%,100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

/* Fade in up for cards */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Images */
.doc-img{
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
    transition: transform 0.5s ease, box-shadow 0.5s ease;
}
.card-hover:hover .doc-img{
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.25);
}

/* Heading & icon */
.card-hover h4{
    color: #fff;
    font-weight: 600;
    margin-bottom: 10px;
}
.card-hover h4 i{
    color: #00f0ff;
    margin-right: 8px;
    animation: iconGlow 2s ease-in-out infinite alternate;
}
@keyframes iconGlow{
    0% { text-shadow: 0 0 5px #00c6ff; }
    100% { text-shadow: 0 0 20px #00f0ff, 0 0 30px #00c6ff; }
}

/* Buttons */
.btn{
    transition: all 0.3s ease;
}
.btn:hover{
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(0,198,255,0.7);
    background: linear-gradient(90deg, #00f0ff, #007bff);
    color: #fff;
}
</style>

</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4 dashboard-title">Doctor Dashboard</h2>
    <div class="row g-4">

```
    <!-- Doctor Profile -->
    <div class="col-md-4">
        <div class="card card-hover p-3 text-center">
            <img src="https://images.pexels.com/photos/8560209/pexels-photo-8560209.jpeg" class="doc-img mb-3" alt="Doctor Profile">
            <h4><i class="fa-solid fa-user-doctor"></i> Doctor Profile</h4>
            <a href="doctor_profile.php" class="btn btn-primary mt-2">Open</a>
        </div>
    </div>

    <!-- Add Doctor -->
    <div class="col-md-4">
        <div class="card card-hover p-3 text-center">
            <img src="https://images.pexels.com/photos/32205061/pexels-photo-32205061.jpeg" class="doc-img mb-3" alt="Add Doctor">
            <h4><i class="fa-solid fa-user-plus"></i> Register Doctor</h4>
            <a href="Registerdoctor.php" class="btn btn-success mt-2">Open</a>
        </div>
    </div>

    <!-- Patient Appointment -->
    <div class="col-md-4">
        <div class="card card-hover p-3 text-center">
            <img src="https://images.pexels.com/photos/29702924/pexels-photo-29702924.jpeg" class="doc-img mb-3" alt="Appointment">
            <h4><i class="fa-solid fa-calendar-check"></i> Patient Appointment</h4>
            <a href="PatientAppoint.php" class="btn btn-warning mt-2">Open</a>
        </div>
    </div>

    <!-- Patient Registration -->
    <div class="col-md-6">
        <div class="card card-hover p-3 text-center">
            <img src="https://images.pexels.com/photos/7108318/pexels-photo-7108318.jpeg" class="doc-img mb-3" alt="Patient Registration">
            <h4><i class="fa-solid fa-address-card"></i> Patient Registration</h4>
            <a href="PatientRegistration.php" class="btn btn-info mt-2 text-white">Open</a>
        </div>
    </div>

    <!-- View Appointments -->
    <div class="col-md-6">
        <div class="card card-hover p-3 text-center">
            <img src="https://images.pexels.com/photos/6193194/pexels-photo-6193194.jpeg" class="doc-img mb-3" alt="View Appointments">
            <h4><i class="fa-solid fa-list-check"></i> View Appointments</h4>
            <a href="Viewappointments.php" class="btn btn-danger mt-2">Open</a>
        </div>
    </div>

</div>
```

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
