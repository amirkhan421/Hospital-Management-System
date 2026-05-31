<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: Patientlogin.php");
    exit();
}

$username = $_SESSION['username'];

$con = mysqli_connect("localhost", "root", "", "clinic");
if(!$con){
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Fetch patient info from registration
$query = "SELECT * FROM `patient_register_login` WHERE username='$username'";
$result = mysqli_query($con, $query);
if(!$result){
    die("Patient query failed: " . mysqli_error($con));
}
$patient_data = mysqli_fetch_assoc($result);

if (!$patient_data) {
    die("<div class='alert alert-danger text-center'>Patient not found in database.</div>");
}

// Fetch all appointments for this patient
$appointments_query = "SELECT * FROM `appointments` WHERE username='$username' ORDER BY appointment_date DESC, appointment_time DESC";
$appointments_result = mysqli_query($con, $appointments_query);
if(!$appointments_result){
    die("Appointments query failed: " . mysqli_error($con));
}

$appointments = [];
while($row = mysqli_fetch_assoc($appointments_result)) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing styles here - unchanged */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .profile-container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 50px 30px;
            text-align: center;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .profile-name {
            font-size: 32px;
            font-weight: 700;
        }
        .profile-email {
            font-size: 18px;
            opacity: 0.9;
        }
        .profile-body { padding: 40px; }
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #6a11cb;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .info-title {
            color: #6a11cb;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .info-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 18px;
        }
        .info-content { flex: 1; display: flex; gap: 15px; flex-wrap: wrap; }
        .info-label { font-weight: 700; min-width: 180px; color: #495057; font-size: 15px; }
        .info-value { color: #212529; font-weight: 500; flex: 1; padding: 8px 15px; background: #f8f9fa; border-radius: 8px; border-left: 3px solid #6a11cb; }
        
        /* Appointment Table Styles */
        .appointment-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .appointment-table th {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .appointment-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .appointment-table tr:hover {
            background-color: #f8f9fa;
        }
        .status-badge { 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 13px; 
            display: inline-block;
        }
        .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .status-completed { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .info-item { flex-direction: column; align-items: flex-start; }
            .info-icon { margin-bottom: 12px; margin-right: 0; }
            .info-content { width: 100%; flex-direction: column; }
            .info-label { min-width: auto; margin-bottom: 5px; }
            .info-value { width: 100%; }
            .appointment-table { font-size: 14px; }
            .appointment-table th, .appointment-table td { padding: 8px 10px; }
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-custom {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
<div class="profile-container">
    <div class="profile-header">
        <img src="https://cdn-icons-png.flaticon.com/512/848/848043.png" class="profile-img" alt="Profile Picture">
        <p class="profile-email"><?php echo htmlspecialchars($patient_data['Email'] ?? 'N/A'); ?></p>
        <div class="mt-3">
            <a href="PatientAppoint.php" class="btn-custom">
                <i class="fas fa-calendar-plus"></i> Book New Appointment
            </a>
        </div>
    </div>

    <div class="profile-body">
        <!-- Personal Information Card -->
        <div class="info-card">
            <h4 class="info-title">
                <span><i class="fas fa-user-circle me-2"></i> Registration Information</span>
            </h4>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-at"></i></div>
                <div class="info-content">
                    <span class="info-label">Username:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Username'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-content">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Email'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-phone"></i></div>
                <div class="info-content">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Phone'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-venus-mars"></i></div>
                <div class="info-content">
                    <span class="info-label">Gender:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Gender'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-birthday-cake"></i></div>
                <div class="info-content">
                    <span class="info-label">Age:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Age'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-tint"></i></div>
                <div class="info-content">
                    <span class="info-label">Blood Group:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Blood_Group'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-content">
                    <span class="info-label">Address:</span>
                    <span class="info-value"><?php echo htmlspecialchars($patient_data['Address'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <!-- Appointments History Card -->
        <div class="info-card">
            <h4 class="info-title">
                <span><i class="fas fa-calendar-alt me-2"></i> Appointment History</span>
                <span class="badge bg-primary"><?php echo count($appointments); ?> Appointments</span>
            </h4>
            
            <?php if (empty($appointments)): ?>
                <div class="no-data">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <h5>No Appointments Found</h5>
                    <p>You haven't booked any appointments yet.</p>
                    <a href="PatientAppoint.php" class="btn-custom mt-2">
                        <i class="fas fa-calendar-plus"></i> Book Your First Appointment
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="appointment-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Message</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['appointment_date'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_time'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($appointment['message'] ?? 'No message'); ?></td>
                                <td>
                                    <?php
                                    $status = $appointment['status'] ?? 'pending';
                                    $badgeClass = 'status-pending';
                                    if ($status == 'confirmed') $badgeClass = 'status-confirmed';
                                    elseif ($status == 'completed') $badgeClass = 'status-completed';
                                    elseif ($status == 'cancelled') $badgeClass = 'status-cancelled';
                                    echo '<span class="status-badge '.$badgeClass.'">'.ucfirst($status).'</span>';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
