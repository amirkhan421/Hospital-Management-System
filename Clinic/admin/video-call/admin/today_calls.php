<?php
session_start();

// ✅ **CORRECT SESSION CHECK** - یہاں doctor کی session check کریں
if(!isset($_SESSION['is_doctor_logged_in']) || $_SESSION['is_doctor_logged_in'] !== true){
    header("Location: doctor_login_call.php");
    exit();
}

// ✅ **NEW: Check if session is about to expire (for security)**
$session_lifetime = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    // Session expired, destroy it
    session_unset();
    session_destroy();
    header("Location: doctor_login_call.php?session=expired");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

$conn = mysqli_connect("localhost", "root", "", "clinic");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ **Doctor ID session سے correct طریقے سے لے**
$doctor_id = $_SESSION['doctor_id'];

// Get today's date
$today = date('Y-m-d');

// CHANGE TABLE NAME from todays_calls to appointmentcall
$query = "SELECT * FROM appointmentcall 
          WHERE doctor_id='$doctor_id' 
          AND appointment_date >= '$today'
          ORDER BY appointment_date ASC, appointment_time ASC";
$result = mysqli_query($conn, $query);

// Check if query was successful
if(!$result) {
    die("Error: " . mysqli_error($conn));
}

// Get doctor info for display
$doctor_query = mysqli_query($conn, "SELECT * FROM doctors_register_call WHERE id='$doctor_id'");
$doctor_info = mysqli_fetch_assoc($doctor_query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Today's Calls</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f8f9fa;
    padding-top: 20px;
}
.card-box{
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,.1);
    transition: transform 0.3s;
}
.card-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,.15);
}
.card-header {
    font-weight: bold;
}
.container {
    max-width: 1200px;
}
.navbar {
    margin-bottom: 30px;
}
</style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-user-md me-2"></i>Doctor Dashboard
        </a>
        <div class="navbar-nav ms-auto">
            <span class="nav-item nav-link text-white">
                <i class="fas fa-user me-1"></i>
                Welcome, Dr. <?php echo htmlspecialchars($_SESSION['doctor_name'] ?? 'Doctor'); ?>
            </span>
            <a class="nav-item nav-link text-white" href="logoutcalladmin.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-primary">
                <i class="fas fa-calendar-check me-2"></i>Today's Appointments
            </h3>
            <p class="text-muted">
                Total Appointments: <span class="badge bg-primary"><?php echo mysqli_num_rows($result); ?></span>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="logoutcalladmin.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>

    <?php if(mysqli_num_rows($result) == 0): ?>
    <div class="alert alert-info text-center py-4">
        <i class="fas fa-calendar-times fa-3x mb-3"></i>
        <h4>No appointments found for today.</h4>
        <p class="mb-0">You have no scheduled appointments at the moment.</p>
    </div>
    <?php else: ?>
    <div class="row">
    <?php 
    $counter = 0;
    while($row = mysqli_fetch_assoc($result)): 
        $counter++;
        
        // Status based styling
        $status_class = '';
        switch(strtolower($row['status'])) {
            case 'pending': $status_class = 'bg-warning text-dark'; break;
            case 'confirmed': $status_class = 'bg-success'; break;
            case 'completed': $status_class = 'bg-info'; break;
            case 'cancelled': $status_class = 'bg-secondary'; break;
            default: $status_class = 'bg-primary';
        }
    ?>
    <div class="col-md-4 mb-4">
        <div class="card card-box h-100">
            <div class="card-header <?php echo $status_class; ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-clock me-1"></i>
                        <?= $row['appointment_time'] ?>
                    </span>
                    <span class="badge bg-light text-dark">
                        #<?= $counter ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="fas fa-user-injured me-2"></i>
                    <?= htmlspecialchars($row['patient_name']) ?>
                </h5>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-calendar-day me-2"></i>Appointment Details
                    </h6>
                    <p class="mb-1">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Date:</strong> <?= $row['appointment_date'] ?>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Time:</strong> <?= date('h:i A', strtotime($row['appointment_time'])) ?>
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-stethoscope me-2"></i>
                        <strong>Status:</strong> 
                        <span class="badge <?php echo $status_class; ?>">
                            <?= $row['status'] ?>
                        </span>
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-file-medical me-2"></i>Medical Problem
                    </h6>
                    <p class="card-text bg-light p-3 rounded">
                        <?= htmlspecialchars($row['problem']) ?>
                    </p>
                </div>
                
                <?php if(!empty($row['doctor_notes'])): ?>
                <div>
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-notes-medical me-2"></i>Doctor's Notes
                    </h6>
                    <p class="card-text bg-info bg-opacity-10 p-3 rounded">
                        <?= htmlspecialchars($row['doctor_notes']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-transparent border-top-0">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        <i class="fas fa-id-badge me-1"></i>
                        Appointment ID: <?= $row['id'] ?>
                    </small>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-notes-medical me-1"></i> Add Notes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    </div>
    <?php endif; ?>
    
    <!-- Summary Section -->
    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="card mt-4 border-primary">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-chart-bar me-2"></i>Appointment Summary
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <h4 class="text-primary"><?php echo $counter; ?></h4>
                        <p class="mb-0">Total Appointments</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <h4 class="text-warning">
                            <?php 
                            // Count pending appointments
                            mysqli_data_seek($result, 0);
                            $pending_count = 0;
                            while($row = mysqli_fetch_assoc($result)) {
                                if(strtolower($row['status']) == 'pending') {
                                    $pending_count++;
                                }
                            }
                            echo $pending_count;
                            ?>
                        </h4>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <h4 class="text-success">
                            <?php 
                            // Count confirmed appointments
                            mysqli_data_seek($result, 0);
                            $confirmed_count = 0;
                            while($row = mysqli_fetch_assoc($result)) {
                                if(strtolower($row['status']) == 'confirmed') {
                                    $confirmed_count++;
                                }
                            }
                            echo $confirmed_count;
                            ?>
                        </h4>
                        <p class="mb-0">Confirmed</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <h4 class="text-info">
                            <?php 
                            // Count today's appointments
                            $today = date('Y-m-d');
                            mysqli_data_seek($result, 0);
                            $today_count = 0;
                            while($row = mysqli_fetch_assoc($result)) {
                                if($row['appointment_date'] == $today) {
                                    $today_count++;
                                }
                            }
                            echo $today_count;
                            ?>
                        </h4>
                        <p class="mb-0">Today's</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<script>
// Simple JavaScript for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add click animation to cards
    const cards = document.querySelectorAll('.card-box');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Auto-refresh page every 30 seconds for new appointments
    setTimeout(() => {
        location.reload();
    }, 30000);
});
</script>

</body>
</html>