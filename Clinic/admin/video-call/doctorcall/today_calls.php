<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: doctor_login_call.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "clinic");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$doctor_id = $_SESSION['doctor_id'];

// Get doctor name for welcome message
$doctor_query = "SELECT full_name FROM doctors_register_call WHERE id='$doctor_id'";
$doctor_result = mysqli_query($conn, $doctor_query);
$doctor_name = "Doctor";
if($doctor_result && mysqli_num_rows($doctor_result) > 0) {
    $doctor_row = mysqli_fetch_assoc($doctor_result);
    $doctor_name = $doctor_row['full_name'];
}

// Fetch appointments with patient details
$query = "SELECT a.*, 
          DATE_FORMAT(a.appointment_date, '%d %b, %Y') as formatted_date,
          TIME_FORMAT(a.appointment_time, '%h:%i %p') as formatted_time
          FROM appointmentcall a 
          WHERE a.doctor_id='$doctor_id' 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = mysqli_query($conn, $query);

// Get statistics
$total_query = "SELECT COUNT(*) as total FROM appointmentcall WHERE doctor_id='$doctor_id'";
$total_result = mysqli_query($conn, $total_query);
$total_appointments = mysqli_fetch_assoc($total_result)['total'];

$pending_query = "SELECT COUNT(*) as pending FROM appointmentcall WHERE doctor_id='$doctor_id' AND status='Pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_appointments = mysqli_fetch_assoc($pending_result)['pending'];

$completed_query = "SELECT COUNT(*) as completed FROM appointmentcall WHERE doctor_id='$doctor_id' AND status='Completed'";
$completed_result = mysqli_query($conn, $completed_query);
$completed_appointments = mysqli_fetch_assoc($completed_result)['completed'];

// Check if query was successful
if(!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - AWCC Clinic</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* 3D Animated Background */
        .bg-3d {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }
        
        .cube {
            position: absolute;
            top: 80vh;
            left: 45vw;
            width: 10px;
            height: 10px;
            border: solid 1px rgba(255, 255, 255, 0.2);
            transform-origin: top left;
            transform: scale(0) rotate(0deg) translate(-50%, -50%);
            animation: cube 12s ease-in forwards infinite;
        }
        
        .cube:nth-child(2n) {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .cube:nth-child(2) {
            animation-delay: 2s;
            left: 25vw;
            top: 40vh;
        }
        
        .cube:nth-child(3) {
            animation-delay: 4s;
            left: 75vw;
            top: 50vh;
        }
        
        .cube:nth-child(4) {
            animation-delay: 6s;
            left: 90vw;
            top: 10vh;
        }
        
        .cube:nth-child(5) {
            animation-delay: 8s;
            left: 10vw;
            top: 85vh;
        }
        
        .cube:nth-child(6) {
            animation-delay: 10s;
            left: 50vw;
            top: 10vh;
        }
        
        @keyframes cube {
            from {
                transform: scale(0) rotate(0deg) translate(-50%, -50%);
                opacity: 1;
            }
            to {
                transform: scale(20) rotate(960deg) translate(-50%, -50%);
                opacity: 0;
            }
        }
        
        /* Floating Particles */
        .particles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-20px) translateX(10px);
            }
            50% {
                transform: translateY(10px) translateX(-10px);
            }
            75% {
                transform: translateY(20px) translateX(5px);
            }
        }
        
        /* Main Container */
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        /* Welcome Header */
        .welcome-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            transform-style: preserve-3d;
            animation: slideInDown 1s ease;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }
        
        .welcome-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .welcome-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 25px;
            color: white;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease;
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
            transform: rotate(45deg);
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.4s; }
        .stat-card:nth-child(3) { animation-delay: 0.6s; }
        .stat-card:nth-child(4) { animation-delay: 0.8s; }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #ff6b6b, #ee5253);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 20px rgba(238, 82, 83, 0.3);
            animation: fadeInRight 1s ease;
        }
        
        .btn-logout:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 30px rgba(238, 82, 83, 0.4);
            color: white;
        }
        
        .btn-filter {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-filter:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            color: white;
        }
        
        /* Appointments Grid */
        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .appointment-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            animation: fadeInUp 1s ease;
            position: relative;
            transform-style: preserve-3d;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .appointment-card:hover {
            transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
            box-shadow: -20px 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .card-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: none;
        }
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .status-badge.pending {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .status-badge.completed {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }
        
        .status-badge.cancelled {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .patient-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .patient-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .patient-details {
            flex: 1;
        }
        
        .patient-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .patient-meta {
            display: flex;
            gap: 10px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-icon {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 2px;
        }
        
        .detail-value {
            font-weight: 500;
            color: #333;
        }
        
        .problem-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 0.95rem;
            color: #555;
            border-left: 3px solid #667eea;
        }
        
        .doctor-notes {
            background: #fff3e0;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 0.95rem;
            color: #e67e22;
            border-left: 3px solid #f39c12;
        }
        
        .card-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        
        .btn-action {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        .btn-complete {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }
        
        .btn-view {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-reschedule {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            color: white;
            animation: fadeIn 1s ease;
        }
        
        .empty-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .empty-text {
            opacity: 0.8;
            margin-bottom: 20px;
        }
        
        /* Animations */
        @keyframes slideInDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeInRight {
            from {
                transform: translateX(30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header h1 {
                font-size: 2rem;
            }
            
            .appointments-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-row {
                grid-template-columns: 1fr;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-logout, .btn-filter {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* 3D Hover Effects */
        .hover-3d {
            transition: transform 0.3s ease;
        }
        
        .hover-3d:hover {
            transform: perspective(1000px) rotateX(5deg) rotateY(5deg) translateZ(20px);
        }
        
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <!-- 3D Animated Background -->
    <div class="bg-3d">
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
    </div>
    
    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>
    
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>
    
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-header glass hover-3d">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-stethoscope me-3"></i>Welcome back, Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
                    <p><i class="far fa-calendar-alt me-2"></i><?php echo date('l, F j, Y'); ?></p>
                </div>
                <div class="text-end">
                    <i class="fas fa-user-md fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-row">
            <div class="stat-card glass hover-3d">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $total_appointments; ?></div>
                <div class="stat-label">Total Appointments</div>
            </div>
            
            <div class="stat-card glass hover-3d">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $pending_appointments; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            
            <div class="stat-card glass hover-3d">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $completed_appointments; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            
            <div class="stat-card glass hover-3d">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo mysqli_num_rows($result); ?></div>
                <div class="stat-label">Today's Patients</div>
            </div>
        </div>
        
        <!-- Action Bar -->
        <div class="action-bar">
            <div>
                <h4 class="text-white mb-0"><i class="fas fa-list me-2"></i>Today's Appointments</h4>
            </div>
            <div class="d-flex gap-3">
                <button class="btn-filter glass" onclick="filterAppointments('all')">
                    <i class="fas fa-filter"></i> All
                </button>
                <button class="btn-filter glass" onclick="filterAppointments('pending')">
                    <i class="fas fa-clock"></i> Pending
                </button>
                <button class="btn-filter glass" onclick="filterAppointments('completed')">
                    <i class="fas fa-check"></i> Completed
                </button>
                <a href="logoutcalldoctor.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Appointments Grid -->
        <?php if(mysqli_num_rows($result) == 0): ?>
            <div class="empty-state glass">
                <div class="empty-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="empty-title">No Appointments Found</div>
                <div class="empty-text">You don't have any appointments scheduled for today.</div>
                <button class="btn-filter glass" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        <?php else: ?>
            <div class="appointments-grid" id="appointmentsGrid">
                <?php 
                $counter = 0;
                while($row = mysqli_fetch_assoc($result)): 
                    $counter++;
                    $initial = strtoupper(substr($row['patient_name'], 0, 1));
                    $status_class = strtolower($row['status']);
                ?>
                <div class="appointment-card glass appointment-item" data-status="<?php echo strtolower($row['status']); ?>" style="animation-delay: <?php echo $counter * 0.1; ?>s">
                    <div class="card-header">
                        <span class="fw-bold"><i class="far fa-clock me-2"></i>Appointment</span>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <i class="fas <?php echo $row['status'] == 'Pending' ? 'fa-clock' : ($row['status'] == 'Completed' ? 'fa-check' : 'fa-times'); ?> me-1"></i>
                            <?php echo $row['status']; ?>
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <div class="patient-info">
                            <div class="patient-avatar">
                                <?php echo $initial; ?>
                            </div>
                            <div class="patient-details">
                                <div class="patient-name"><?php echo htmlspecialchars($row['patient_name']); ?></div>
                                <div class="patient-meta">
                                    <span><i class="far fa-id-card me-1"></i>ID: P00<?php echo rand(100, 999); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Appointment Date</div>
                                <div class="detail-value"><?php echo $row['formatted_date']; ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Appointment Time</div>
                                <div class="detail-value"><?php echo $row['formatted_time']; ?></div>
                            </div>
                        </div>
                        
                        <div class="problem-text">
                            <i class="fas fa-notes-medical me-2 text-primary"></i>
                            <strong>Problem:</strong> <?php echo htmlspecialchars($row['problem']); ?>
                        </div>
                        
                        <?php if(!empty($row['doctor_notes'])): ?>
                        <div class="doctor-notes">
                            <i class="fas fa-sticky-note me-2"></i>
                            <strong>Doctor's Notes:</strong> <?php echo htmlspecialchars($row['doctor_notes']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer">
                        <button class="btn-action btn-view" onclick="viewAppointment(<?php echo $row['id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <?php if($row['status'] == 'Pending'): ?>
                        <button class="btn-action btn-complete" onclick="completeAppointment(<?php echo $row['id']; ?>)">
                            <i class="fas fa-check"></i> Complete
                        </button>
                        <button class="btn-action btn-reschedule" onclick="rescheduleAppointment(<?php echo $row['id']; ?>)">
                            <i class="fas fa-clock"></i> Reschedule
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const colors = [
                'rgba(255, 255, 255, 0.1)',
                'rgba(102, 126, 234, 0.1)',
                'rgba(118, 75, 162, 0.1)'
            ];
            
            for(let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 10 + 5;
                const left = Math.random() * 100;
                const top = Math.random() * 100;
                const delay = Math.random() * 5;
                const duration = Math.random() * 10 + 5;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${left}%`;
                particle.style.top = `${top}%`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Filter appointments
        function filterAppointments(status) {
            const items = document.querySelectorAll('.appointment-item');
            const buttons = document.querySelectorAll('.btn-filter');
            
            // Update active button
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Show loading spinner
            document.getElementById('loadingSpinner').style.display = 'block';
            
            setTimeout(() => {
                items.forEach(item => {
                    if(status === 'all' || item.dataset.status === status) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeInUp 0.5s ease';
                    } else {
                        item.style.display = 'none';
                    }
                });
                document.getElementById('loadingSpinner').style.display = 'none';
            }, 500);
        }
        
        // View appointment details
        function viewAppointment(id) {
            window.location.href = 'view_appointment.php?id=' + id;
        }
        
        // Complete appointment
        function completeAppointment(id) {
            if(confirm('Mark this appointment as completed?')) {
                window.location.href = 'complete_appointment.php?id=' + id;
            }
        }
        
        // Reschedule appointment
        function rescheduleAppointment(id) {
            window.location.href = 'reschedule_appointment.php?id=' + id;
        }
        
        // Add 3D tilt effect to cards
        document.querySelectorAll('.appointment-card').forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
        
        // Refresh data periodically
        setInterval(() => {
            location.reload();
        }, 300000); // Refresh every 5 minutes
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Press 'R' to refresh
            if(e.key === 'r' || e.key === 'R') {
                if(!e.ctrlKey) {
                    location.reload();
                }
            }
            
            // Press 'L' to logout
            if(e.key === 'l' || e.key === 'L') {
                if(confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logoutcalldoctor.php';
                }
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            // Add smooth scroll behavior
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Add animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.8s ease';
                    }
                });
            });
            
            document.querySelectorAll('.appointment-card').forEach(card => {
                observer.observe(card);
            });
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            document.body.style.opacity = '0.5';
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                document.body.style.opacity = '1';
            }, 500);
        });
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>