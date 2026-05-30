<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Doctorlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    font-family: "Poppins", sans-serif;
    min-height: 100vh;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 20px;
}

/* Header Card */
.dashboard-card {
    background: #fff;
    border-radius: 25px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 800px;
    padding: 50px 40px;
    text-align: center;
    animation: fadeInDown 1s ease;
    position: relative;
    overflow: hidden;
}
h1 {
    color: #007bff;
    font-weight: 800;
    margin-bottom: 35px;
    font-size: 2.4rem;
    letter-spacing: 1px;
}

/* Header Buttons */
.btn-dashboard {
    width: 48%;
    border-radius: 15px;
    font-weight: 600;
    padding: 18px;
    margin: 10px 1%;
    transition: all 0.4s ease;
    font-size: 1.1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.btn-dashboard:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}

/* Cards Section */
.cards-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 60px;
    gap: 30px;
}

.card-dashboard {
    background: #fff;
    width: 260px;
    border-radius: 25px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0,0,0,0.25);
    transition: all 0.5s ease;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    animation: fadeInUp 1s ease;
}
.card-dashboard:hover {
    transform: translateY(-15px) scale(1.07);
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
}
.card-dashboard i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #007bff;
    transition: all 0.4s ease;
}
.card-dashboard:hover i {
    transform: rotate(15deg) scale(1.3);
}
.card-dashboard h4 {
    margin-bottom: 12px;
    font-weight: 700;
    font-size: 1.3rem;
}
.card-dashboard p {
    font-size: 0.9rem;
    color: #555;
    line-height: 1.5;
    margin-bottom: 0;
}

/* Animations */
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-40px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Footer */
footer {
    margin-top: 40px;
    font-size: 0.9rem;
    color: #fff;
    text-align: center;
}

/* Responsive */
@media(max-width: 992px){
    .btn-dashboard { width: 100%; margin: 10px 0; }
    .cards-container { flex-direction: column; align-items: center; }
    .card-dashboard { width: 85%; }
}
</style>
</head>
<body>

<!-- Header -->
<div class="dashboard-card">
    <h1>Welcome Doctor <?php echo $_SESSION['username']; ?></h1>
    <a href="doctor_profile.php" class="btn btn-primary btn-dashboard"><i class="fa-solid fa-user-doctor me-2"></i>Doctor Profile</a>
    <a href="LogoutDoctor.php" class="btn btn-danger btn-dashboard"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
</div>

<!-- Cards Section -->
<div class="cards-container">

    <div class="card-dashboard">
        <i class="fa-solid fa-calendar-check"></i>
        <h4>Appointments</h4>
        <p>View, schedule, and manage all your patient appointments efficiently. Keep track of timings and updates instantly.</p>
    </div>

    <div class="card-dashboard">
        <i class="fa-solid fa-file-medical"></i>
        <h4>Prescriptions</h4>
        <p>Issue, update, and view prescriptions digitally with detailed records. Ensure accurate medication for your patients.</p>
    </div>

    <div class="card-dashboard">
        <i class="fa-solid fa-user"></i>
        <h4>Profile</h4>
        <p>Manage your personal and professional details securely. Update contact info, specialization, and other essential info.</p>
    </div>

</div>

<footer>© 2025 Hospital Management System</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
