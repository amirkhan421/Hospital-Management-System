<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "clinic");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: patient_login_call.php");
    exit();
}

// Get current logged-in patient username
$patient_username = $_SESSION['username'];

// First, get patient details
$patient_query = "SELECT * FROM patient_register_call WHERE username = '$patient_username'";
$patient_result = mysqli_query($conn, $patient_query);

if (!$patient_result) {
    die("Error in patient query: " . mysqli_error($conn));
}

if (mysqli_num_rows($patient_result) > 0) {
    $patient_data = mysqli_fetch_assoc($patient_result);
    $patient_name = $patient_data['full_name'];
    $patient_id = $patient_data['id'];
    
    // Debug: چیک کریں کہ کون سے کالم موجود ہیں
    $check_columns = mysqli_query($conn, "SHOW COLUMNS FROM patient_register_call");
    $available_columns = [];
    while($col = mysqli_fetch_assoc($check_columns)) {
        $available_columns[] = $col['Field'];
    }
    
    // صرف موجودہ کالموں کو منتخب کریں
    $select_fields = [];
    $possible_fields = ['full_name', 'email', 'phone', 'address', 'gender', 'dob', 'blood_group', 'medical_history', 'created_at'];
    
    foreach ($possible_fields as $field) {
        if (in_array($field, $available_columns)) {
            $select_fields[] = $field;
        }
    }
    
    // اگر کوئی فیلڈ نہ ملے تو بنیادی فیلڈز استعمال کریں
    if (empty($select_fields)) {
        $select_fields = ['full_name', 'email', 'phone', 'created_at'];
    }
    
    $fields_str = implode(', ', $select_fields);
    $profile_query = "SELECT $fields_str FROM patient_register_call WHERE id = $patient_id";
    
    $profile_result = mysqli_query($conn, $profile_query);
    if ($profile_result && mysqli_num_rows($profile_result) > 0) {
        $profile_data = mysqli_fetch_assoc($profile_result);
    } else {
        $profile_data = ['full_name' => $patient_name, 'email' => '', 'phone' => ''];
    }
    
    // ٹیبل کا صحیح نام چیک کریں
    $check_tables = mysqli_query($conn, "SHOW TABLES LIKE 'appointmentcal'");
    if (mysqli_num_rows($check_tables) > 0) {
        $appointment_table = 'appointmentcal';
    } else {
        $check_tables2 = mysqli_query($conn, "SHOW TABLES LIKE 'appointmentcall'");
        if (mysqli_num_rows($check_tables2) > 0) {
            $appointment_table = 'appointmentcall';
        } else {
            $appointment_table = 'appointment'; // ڈیفالٹ
        }
    }
    
    // ڈیبگ: چیک کریں کہ ٹیبل میں کون سے کالم موجود ہیں
    $check_appointment_columns = mysqli_query($conn, "SHOW COLUMNS FROM $appointment_table");
    if (!$check_appointment_columns) {
        die("Error checking appointment table columns: " . mysqli_error($conn));
    }
    
    // اپائنٹمنٹس کے لیے بہتر query - پہلے صرف بنیادی query چلائیں
    $appointments_query = "SELECT a.* FROM $appointment_table a 
                          WHERE a.patient_name = '$patient_name' 
                          OR a.patient_id = '$patient_id'
                          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $appointments_result = mysqli_query($conn, $appointments_query);
    
    if (!$appointments_result) {
        $error_message = "Database Error in appointments query: " . mysqli_error($conn);
        $has_appointments = false;
        // ڈیبگ کے لیے query پرنٹ کریں
        error_log("Failed Query: " . $appointments_query);
    } else {
        $has_appointments = mysqli_num_rows($appointments_result) > 0;
        
        // اگر اپائنٹمنٹس ملیں تو ڈاکٹر کی معلومات fetch کریں
        if ($has_appointments) {
            mysqli_data_seek($appointments_result, 0);
            $all_appointments = [];
            while ($app_row = mysqli_fetch_assoc($appointments_result)) {
                $doctor_info = ['doctor_name' => 'Not Assigned', 'doctor_specialization' => ''];
                
                // اگر doctor_id ہے تو ڈاکٹر کی معلومات fetch کریں
                if (!empty($app_row['doctor_id'])) {
                    $doc_id = $app_row['doctor_id'];
                    $doc_query = "SELECT full_name, specialization FROM doctors_register_call WHERE id = '$doc_id'";
                    $doc_result = mysqli_query($conn, $doc_query);
                    if ($doc_result && mysqli_num_rows($doc_result) > 0) {
                        $doc_data = mysqli_fetch_assoc($doc_result);
                        $doctor_info['doctor_name'] = $doc_data['full_name'];
                        $doctor_info['doctor_specialization'] = $doc_data['specialization'];
                    }
                }
                
                // دونوں معلومات کو ملا دیں
                $all_appointments[] = array_merge($app_row, $doctor_info);
            }
        }
    }
    
    // شماریات کے لیے query
    $stats_query = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
        FROM $appointment_table 
        WHERE patient_name = '$patient_name' OR patient_id = '$patient_id'";
    
    $stats_result = mysqli_query($conn, $stats_query);
    if ($stats_result) {
        $stats_data = mysqli_fetch_assoc($stats_result);
    } else {
        $stats_data = ['total' => 0, 'accepted' => 0, 'pending' => 0, 'cancelled' => 0, 'completed' => 0];
    }
    
} else {
    $error_message = "Patient not found in database!";
    $has_appointments = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | AWC</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #17a2b8;
            --purple-color: #9b59b6;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-left: 6px solid var(--primary-color);
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-top: 4px solid var(--secondary-color);
        }
        
        .stats-card {
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            color: white;
            transition: transform 0.3s ease;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-total { background: linear-gradient(135deg, var(--primary-color), #34495e); }
        .stats-accepted { background: linear-gradient(135deg, var(--success-color), #27ae60); }
        .stats-pending { background: linear-gradient(135deg, var(--warning-color), #f39c12); }
        .stats-cancelled { background: linear-gradient(135deg, var(--danger-color), #c0392b); }
        .stats-completed { background: linear-gradient(135deg, var(--purple-color), #8e44ad); }
        
        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .appointment-card:hover {
            transform: translateX(5px);
        }
        
        .appointment-accepted { border-left-color: var(--success-color); background: #f0fdf4; }
        .appointment-pending { border-left-color: var(--warning-color); background: #fff7ed; }
        .appointment-cancelled { border-left-color: var(--danger-color); background: #fef2f2; }
        .appointment-completed { border-left-color: var(--secondary-color); background: #f0f9ff; }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
        }
        
        .badge-accepted { background: var(--success-color); color: white; }
        .badge-pending { background: var(--warning-color); color: white; }
        .badge-cancelled { background: var(--danger-color); color: white; }
        .badge-completed { background: var(--secondary-color); color: white; }
        
        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-item:last-child { border-bottom: none; }
        
        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }
        
        .info-value {
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .btn-custom {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .dashboard-container { padding: 10px; }
            .header-card, .profile-card { padding: 15px; }
            .stats-card { padding: 15px; }
        }
        
        .doctor-info {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 5px;
        }
        
        .doctor-specialization {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .debug-info {
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Debug Info (صرف ڈیولپمنٹ کے لیے) -->
        <?php if (isset($error_message)): ?>
            <div class="debug-info">
                <strong>Debug Info:</strong><br>
                Patient Name: <?php echo htmlspecialchars($patient_name ?? 'N/A'); ?><br>
                Patient ID: <?php echo $patient_id ?? 'N/A'; ?><br>
                Table Used: <?php echo $appointment_table ?? 'N/A'; ?><br>
                Error: <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Header Section -->
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2"><i class="fas fa-user-injured me-2"></i> Patient Dashboard</h2>
                    <p class="text-muted mb-0">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="patient_choose&profile.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                    <a href="appointmentcall.php" class="btn btn-success btn-sm ms-1">
                        <i class="fas fa-plus me-1"></i> Book Appointment
                    </a>
                    <a href="logoutcallpatient.php" class="btn btn-outline-danger btn-sm ms-1">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="fas fa-user-circle me-2"></i> Personal Information
            </h4>
            
            <div class="row">
                <?php if (isset($profile_data) && is_array($profile_data)): ?>
                    <?php foreach ($profile_data as $key => $value): ?>
                        <?php if (!empty($value) && $key != 'password' && $key != 'id'): ?>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="info-label">
                                        <?php 
                                        $labels = [
                                            'full_name' => '<i class="fas fa-user me-2"></i> Full Name',
                                            'email' => '<i class="fas fa-envelope me-2"></i> Email',
                                            'phone' => '<i class="fas fa-phone me-2"></i> Phone',
                                            'address' => '<i class="fas fa-map-marker-alt me-2"></i> Address',
                                            'gender' => '<i class="fas fa-venus-mars me-2"></i> Gender',
                                            'dob' => '<i class="fas fa-birthday-cake me-2"></i> Date of Birth',
                                            'blood_group' => '<i class="fas fa-tint me-2"></i> Blood Group',
                                            'medical_history' => '<i class="fas fa-file-medical me-2"></i> Medical History',
                                            'created_at' => '<i class="fas fa-history me-2"></i> Member Since'
                                        ];
                                        echo $labels[$key] ?? '<i class="fas fa-info-circle me-2"></i> ' . ucwords(str_replace('_', ' ', $key));
                                        ?>
                                    </span>
                                    <span class="info-value">
                                        <?php 
                                        if ($key == 'created_at') {
                                            echo date('M d, Y', strtotime($value));
                                        } else {
                                            echo htmlspecialchars($value);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Profile information not found!
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-6">
                <div class="stats-card stats-total">
                    <h6 class="mb-1"><i class="fas fa-calendar-alt me-1"></i> Total</h6>
                    <h3 class="mb-0"><?php echo $stats_data['total'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stats-card stats-accepted">
                    <h6 class="mb-1"><i class="fas fa-check-circle me-1"></i> Accepted</h6>
                    <h3 class="mb-0"><?php echo $stats_data['accepted'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stats-card stats-pending">
                    <h6 class="mb-1"><i class="fas fa-clock me-1"></i> Pending</h6>
                    <h3 class="mb-0"><?php echo $stats_data['pending'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stats-card stats-cancelled">
                    <h6 class="mb-1"><i class="fas fa-times-circle me-1"></i> Cancelled</h6>
                    <h3 class="mb-0"><?php echo $stats_data['cancelled'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stats-card stats-completed">
                    <h6 class="mb-1"><i class="fas fa-check-double me-1"></i> Completed</h6>
                    <h3 class="mb-0"><?php echo $stats_data['completed'] ?? 0; ?></h3>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stats-card" style="background: linear-gradient(135deg, var(--info-color), #2980b9);">
                    <h6 class="mb-1"><i class="fas fa-calendar-day me-1"></i> Today</h6>
                    <h3 class="mb-0"><?php 
                        if (isset($appointment_table)) {
                            $today = date('Y-m-d');
                            $today_query = "SELECT COUNT(*) as today FROM $appointment_table 
                                          WHERE (patient_name = '$patient_name' OR patient_id = '$patient_id') 
                                          AND appointment_date = '$today'";
                            $today_result = mysqli_query($conn, $today_query);
                            if ($today_result) {
                                $today_data = mysqli_fetch_assoc($today_result);
                                echo $today_data['today'] ?? 0;
                            } else {
                                echo "0";
                            }
                        } else {
                            echo "0";
                        }
                    ?></h3>
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="profile-card">
            <h4 class="section-title">
                <i class="fas fa-calendar-check me-2"></i> My Appointments
                <span class="ms-auto" style="font-size: 0.9rem; color: #666;">
                    <i class="fas fa-list me-1"></i> Total: <?php echo $stats_data['total'] ?? 0; ?>
                </span>
            </h4>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($has_appointments && isset($all_appointments)): ?>
                <?php foreach ($all_appointments as $row): ?>
                    <?php
                    $status = $row['status'] ?? 'Pending';
                    $status_lower = strtolower($status);
                    $status_class = '';
                    $badge_class = '';
                    
                    if ($status_lower == 'accepted' || $status_lower == 'approved') {
                        $status_class = 'appointment-accepted';
                        $badge_class = 'badge-accepted';
                    } elseif ($status_lower == 'pending') {
                        $status_class = 'appointment-pending';
                        $badge_class = 'badge-pending';
                    } elseif ($status_lower == 'cancelled' || $status_lower == 'rejected') {
                        $status_class = 'appointment-cancelled';
                        $badge_class = 'badge-cancelled';
                    } else {
                        $status_class = 'appointment-completed';
                        $badge_class = 'badge-completed';
                    }
                    
                    $doctor_name = $row['doctor_name'] ?? 'Not Assigned';
                    $doctor_specialization = $row['doctor_specialization'] ?? '';
                    ?>
                    
                    <div class="appointment-card <?php echo $status_class; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="doctor-info">
                                    <h6 class="mb-1">
                                        <i class="fas fa-user-md me-1"></i> 
                                        <?php echo htmlspecialchars($doctor_name); ?>
                                    </h6>
                                    <?php if (!empty($doctor_specialization)): ?>
                                        <p class="doctor-specialization mb-1">
                                            <i class="fas fa-stethoscope me-1"></i>
                                            <?php echo htmlspecialchars($doctor_specialization); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1 small text-muted mt-2">
                                    <i class="fas fa-calendar me-1"></i> 
                                    <?php echo isset($row['appointment_date']) ? date('d M Y', strtotime($row['appointment_date'])) : 'N/A'; ?>
                                </p>
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-clock me-1"></i> 
                                    <?php echo isset($row['appointment_time']) ? date('h:i A', strtotime($row['appointment_time'])) : 'N/A'; ?>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Medical Issue:</strong></p>
                                <p class="mb-1 small"><?php echo htmlspecialchars($row['problem'] ?? 'Not specified'); ?></p>
                                <?php if (!empty($row['doctor_notes'])): ?>
                                    <p class="mb-0 small text-muted">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        <strong>Doctor's Note:</strong> <?php echo substr(htmlspecialchars($row['doctor_notes']), 0, 50); ?>...
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <span class="status-badge <?php echo $badge_class; ?> mb-2">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                    <?php echo ucfirst($status); ?>
                                </span>
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i>ID: <?php echo $row['id'] ?? 'N/A'; ?>
                                    </small>
                                </div>
                                <?php if (!empty($row['doctor_id'])): ?>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-user-md me-1"></i>
                                            Doctor ID: <?php echo $row['doctor_id']; ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 text-end">
                                <?php if ($status_lower == 'accepted' || $status_lower == 'approved'): ?>
                                    <?php
                                    $appointment_datetime = ($row['appointment_date'] ?? date('Y-m-d')) . ' ' . ($row['appointment_time'] ?? '00:00:00');
                                    $current_datetime = date('Y-m-d H:i:s');
                                    $can_join = (strtotime($appointment_datetime) <= strtotime($current_datetime));
                                    ?>
                                    <?php if ($can_join): ?>
                                        <a href="video-call.php?appointment_id=<?php echo $row['id'] ?? ''; ?>" 
                                           class="btn btn-success btn-sm mb-1">
                                            <i class="fas fa-video me-1"></i> Join Call
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i> Waiting
                                        </span>
                                    <?php endif; ?>
                                <?php elseif ($status_lower == 'pending'): ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-hourglass-half me-1"></i> Under Review
                                    </span>
                                <?php endif; ?>
                                
                                <div class="mt-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No Appointments Found</h5>
                    <p class="text-muted mb-3">You haven't booked any appointments yet.</p>
                    <a href="appointmentcall.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Book Your First Appointment
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 text-muted small">
            <p class="mb-1"><i class="fas fa-hospital me-1"></i> Clinic Management System • <?php echo date('F j, Y'); ?></p>
            <p class="mb-0"><i class="fas fa-headset me-1"></i> Need help? Contact: support@clinic.com | Call: (021) 123-4567</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>