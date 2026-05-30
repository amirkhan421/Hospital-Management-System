<?php
session_start();

// Database connection
$con = mysqli_connect("localhost", "root", "", "clinic");
if(!$con) die("Connection failed: " . mysqli_connect_error());

$msg = "";
$success = false;

// Check if patient is logged in
if(!isset($_SESSION['username'])) {
    header("Location: patient_login_call.php");
    exit();
}

$patient_username = $_SESSION['username'];

// Get current patient details
$patient_query = mysqli_query($con, "SELECT id, full_name FROM patient_register_call WHERE username = '$patient_username'");
if(mysqli_num_rows($patient_query) > 0) {
    $patient = mysqli_fetch_assoc($patient_query);
    $_SESSION['patient_id'] = $patient['id'];
    $_SESSION['patient_name'] = $patient['full_name'];
} else {
    die("Patient not found. Please login again.");
}

// Fetch available doctors
$doctors_result = mysqli_query($con, "SELECT * FROM doctors_register_call ORDER BY full_name");
if(!$doctors_result) die("Error fetching doctors: " . mysqli_error($con));

// Generate next 4 Sundays
function getNextSundays($count = 4) {
    $sundays = [];
    $today = new DateTime();
    $dayOfWeek = $today->format('w');
    
    // Find next Sunday
    $daysUntilSunday = (7 - $dayOfWeek) % 7;
    $nextSunday = clone $today;
    $nextSunday->modify("+$daysUntilSunday days");
    
    // Get next $count Sundays
    for($i = 0; $i < $count; $i++) {
        $sundays[] = [
            'date' => $nextSunday->format('Y-m-d'),
            'display' => $nextSunday->format('d M, Y (l)')
        ];
        $nextSunday->modify('+7 days');
    }
    
    return $sundays;
}

// Generate time slots (8 AM to 8 PM)
function getTimeSlots() {
    $timeSlots = [];
    for($hour = 8; $hour < 20; $hour++) {
        $time = sprintf("%02d:00", $hour);
        $display = date('h:i A', strtotime($time));
        $timeSlots[] = [
            'time' => $time,
            'display' => $display
        ];
    }
    return $timeSlots;
}

$availableSundays = getNextSundays(4);
$timeSlots = getTimeSlots();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = mysqli_real_escape_string($con, $_POST['doctor_id']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $time = mysqli_real_escape_string($con, $_POST['time']);
    $problem = mysqli_real_escape_string($con, $_POST['problem']);

    $patient_id = $_SESSION['patient_id'];
    $patient_name = $_SESSION['patient_name'];

    // Validate selected date is in available Sundays
    $validDate = false;
    foreach($availableSundays as $sunday) {
        if($sunday['date'] == $date) {
            $validDate = true;
            break;
        }
    }
    
    if(!$validDate) {
        $msg = "Please select a valid date from the available Sundays!";
        $success = false;
    } else {
        // Validate time
        $validTime = false;
        foreach($timeSlots as $slot) {
            if($slot['time'] == $time) {
                $validTime = true;
                break;
            }
        }
        
        if(!$validTime) {
            $msg = "Please select a valid time slot!";
            $success = false;
        } else {
            // Check if doctor is available at that time
            $check_availability = mysqli_query($con, 
                "SELECT id FROM appointmentcall 
                 WHERE doctor_id = '$doctor_id' 
                 AND appointment_date = '$date' 
                 AND appointment_time = '$time'"
            );

            if(mysqli_num_rows($check_availability) > 0) {
                $msg = "Doctor is not available at this time. Please choose another time.";
                $success = false;
            } else {
                // Get doctor details for notification
                $doctor_query = mysqli_query($con, "SELECT full_name, specialization FROM doctors_register_call WHERE id = '$doctor_id'");
                $doctor_data = mysqli_fetch_assoc($doctor_query);

                // Insert appointment
                $query = "INSERT INTO appointmentcall 
                         (doctor_id, patient_id, patient_name, appointment_date, appointment_time, problem, status) 
                         VALUES ('$doctor_id', '$patient_id', '$patient_name', '$date', '$time', '$problem', 'Pending')";

                if(mysqli_query($con, $query)) {
                    $msg = "Appointment booked successfully with Dr. " . $doctor_data['full_name'] . " (" . $doctor_data['specialization'] . ") on " . date('d M, Y', strtotime($date)) . " at " . date('h:i A', strtotime($time)) . "!";
                    $success = true;
                    
                    // Clear form fields after successful submission
                    $_POST = array();
                } else {
                    $msg = "Error: " . mysqli_error($con);
                    $success = false;
                }
            }
        }
    }
}

// Reset doctors result pointer
mysqli_data_seek($doctors_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | AWC Clinic</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #2196f3;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --light-bg: #f0f8ff;
        }
        
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%230055cc" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            z-index: -1;
        }
        
        .booking-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .booking-header h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .booking-header h3::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 2px;
        }
        
        .patient-info-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .patient-avatar {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(33, 150, 243, 0.25);
            transform: translateY(-2px);
        }
        
        .info-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .info-note i {
            color: #f39c12;
            margin-top: 2px;
        }
        
        .btn-book {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(13, 71, 161, 0.3);
            background: linear-gradient(135deg, #0b3d91, #1e88e5);
        }
        
        .btn-book:active {
            transform: translateY(-1px);
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .appointment-preview {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            border: 2px dashed #dee2e6;
            display: none;
        }
        
        .preview-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .preview-label {
            font-weight: 600;
            color: #555;
        }
        
        .preview-value {
            color: #333;
            font-weight: 500;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            font-size: 40px;
            animation: float 15s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
        
        .success-animation {
            text-align: center;
            padding: 40px 20px;
            display: none;
        }
        
        .success-animation i {
            font-size: 80px;
            color: var(--success-color);
            margin-bottom: 20px;
            animation: bounceIn 1s;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .doctor-select-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .doctor-select-card:hover {
            border-color: var(--secondary-color);
            transform: translateX(5px);
        }
        
        .doctor-select-card.selected {
            border-color: var(--primary-color);
            background: #e3f2fd;
        }
        
        .doctor-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        @media (max-width: 768px) {
            .booking-card {
                padding: 25px;
                margin: 20px;
            }
            
            .patient-info-box {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .doctor-select-card {
                flex-direction: column;
                text-align: center;
            }
        }
        
        .date-badge {
            display: inline-block;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 8px 15px;
            margin: 5px;
            border-radius: 20px;
            border: 2px solid #4caf50;
            font-weight: 600;
        }
        
        .available-dates-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-card animate__animated animate__fadeInUp">
            <!-- Floating elements -->
            <div class="floating-elements">
                <div class="floating-icon" style="top:10%; left:5%; animation-delay: 0s;"><i class="fas fa-heartbeat"></i></div>
                <div class="floating-icon" style="top:20%; right:10%; animation-delay: 3s;"><i class="fas fa-stethoscope"></i></div>
                <div class="floating-icon" style="top:60%; left:15%; animation-delay: 6s;"><i class="fas fa-user-md"></i></div>
                <div class="floating-icon" style="top:70%; right:5%; animation-delay: 9s;"><i class="fas fa-hospital"></i></div>
            </div>
            
            <!-- Success animation (hidden by default) -->
            <div class="success-animation" id="successAnimation">
                <i class="fas fa-check-circle"></i>
                <h4 class="text-success">Appointment Booked Successfully!</h4>
                <p id="successMessage"></p>
                <button class="btn btn-primary mt-3" onclick="window.location.href='patient_choose&profile.php'">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </button>
            </div>
            
            <!-- Main form (shown by default) -->
            <div id="bookingForm">
                <div class="booking-header">
                    <h3><i class="fas fa-calendar-check me-2"></i>Book Your Appointment</h3>
                    <p class="text-muted">Schedule your consultation with our expert doctors</p>
                </div>
                
                <!-- Patient Info -->
                <div class="patient-info-box">
                    <div class="patient-avatar">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['patient_name']); ?></h5>
                        <p class="mb-0 text-muted">Patient ID: <?php echo $_SESSION['patient_id']; ?></p>
                    </div>
                    <a href="patient_choose&profile.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-home me-1"></i>Dashboard
                    </a>
                </div>
                
                <!-- Message Alert -->
                <?php if($msg): ?>
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                        <?php echo htmlspecialchars($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Booking Form -->
                <form method="POST" id="appointmentForm">
                    <!-- Doctor Selection -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user-md text-primary"></i>Select Doctor
                        </label>
                        <select name="doctor_id" class="form-select" required id="doctorSelect">
                            <option value="">-- Choose Doctor --</option>
                            <?php 
                            $doctor_index = 0;
                            while($doc = mysqli_fetch_assoc($doctors_result)): 
                            ?>
                                <option value="<?php echo $doc['id']; ?>">
                                    Dr. <?php echo htmlspecialchars($doc['full_name']); ?> - <?php echo htmlspecialchars($doc['specialization']); ?>
                                </option>
                            <?php 
                            $doctor_index++;
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    
                    <!-- Date Selection -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-day text-primary"></i>Select Appointment Date
                        </label>
                        <select name="date" class="form-select" required id="dateSelect">
                            <option value="">-- Choose Date --</option>
                            <?php foreach($availableSundays as $sunday): ?>
                                <option value="<?php echo $sunday['date']; ?>">
                                    <?php echo $sunday['display']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="available-dates-box mt-3">
                            <h6 class="mb-2"><i class="fas fa-calendar-check me-2 text-success"></i>Available Sundays:</h6>
                            <?php foreach($availableSundays as $sunday): ?>
                                <span class="date-badge">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo date('d M', strtotime($sunday['date'])); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Time Selection -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-clock text-primary"></i>Select Appointment Time
                        </label>
                        <select name="time" class="form-select" required id="timeSelect">
                            <option value="">-- Choose Time --</option>
                            <?php foreach($timeSlots as $slot): ?>
                                <option value="<?php echo $slot['time']; ?>">
                                    <?php echo $slot['display']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-1 d-block">Available slots from 8:00 AM to 8:00 PM</small>
                    </div>
                    
                    <!-- Problem Description -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-file-medical text-primary"></i>Describe Your Problem
                        </label>
                        <textarea name="problem" class="form-control" rows="4" 
                                  placeholder="Please describe your symptoms or medical concern in detail..." 
                                  required id="problemText"><?php echo isset($_POST['problem']) ? htmlspecialchars($_POST['problem']) : ''; ?></textarea>
                        <small class="text-muted">Be as detailed as possible for better diagnosis</small>
                    </div>
                    
                    <!-- Information Note -->
                    <div class="info-note">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Important Information:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Appointments are only available on Sundays</li>
                                <li>Only next 4 Sundays are available for booking</li>
                                <li>Consultation time is 30 minutes per appointment</li>
                                <li>Cancellation must be done at least 24 hours in advance</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-outline-secondary flex-grow-1" onclick="showPreview()">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                        <button type="submit" class="btn-book flex-grow-1" id="submitBtn">
                            <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                            <div class="loading-spinner" id="loadingSpinner"></div>
                        </button>
                    </div>
                </form>
                
                <!-- Appointment Preview -->
                <div class="appointment-preview" id="appointmentPreview">
                    <h5 class="mb-3"><i class="fas fa-file-alt me-2"></i>Appointment Preview</h5>
                    <div class="preview-item">
                        <span class="preview-label">Doctor:</span>
                        <span class="preview-value" id="previewDoctor">Not selected</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Date:</span>
                        <span class="preview-value" id="previewDate">Not selected</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Time:</span>
                        <span class="preview-value" id="previewTime">Not selected</span>
                    </div>
                    <div class="preview-item">
                        <span class="preview-label">Problem:</span>
                        <span class="preview-value" id="previewProblem">Not specified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Update preview
        function updatePreview() {
            const doctorSelect = document.getElementById('doctorSelect');
            const dateSelect = document.getElementById('dateSelect');
            const timeSelect = document.getElementById('timeSelect');
            const problemText = document.getElementById('problemText');
            
            // Update preview
            document.getElementById('previewDoctor').textContent = doctorSelect.options[doctorSelect.selectedIndex].text || 'Not selected';
            document.getElementById('previewDate').textContent = dateSelect.options[dateSelect.selectedIndex].text || 'Not selected';
            document.getElementById('previewTime').textContent = timeSelect.options[timeSelect.selectedIndex].text || 'Not selected';
            document.getElementById('previewProblem').textContent = problemText.value || 'Not specified';
        }

        // Show preview
        function showPreview() {
            // Validate all fields are filled
            const doctorSelect = document.getElementById('doctorSelect');
            const dateSelect = document.getElementById('dateSelect');
            const timeSelect = document.getElementById('timeSelect');
            const problemText = document.getElementById('problemText');
            
            if (!doctorSelect.value) {
                alert('Please select a doctor first!');
                doctorSelect.focus();
                return;
            }
            
            if (!dateSelect.value) {
                alert('Please select a date!');
                dateSelect.focus();
                return;
            }
            
            if (!timeSelect.value) {
                alert('Please select a time!');
                timeSelect.focus();
                return;
            }
            
            if (!problemText.value.trim()) {
                alert('Please describe your problem!');
                problemText.focus();
                return;
            }
            
            updatePreview();
            const preview = document.getElementById('appointmentPreview');
            preview.style.display = 'block';
            preview.scrollIntoView({ behavior: 'smooth' });
        }

        // Form submission
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('loadingSpinner');
            
            // Validate all fields
            const doctorSelect = document.getElementById('doctorSelect');
            const dateSelect = document.getElementById('dateSelect');
            const timeSelect = document.getElementById('timeSelect');
            const problemText = document.getElementById('problemText');
            
            if (!doctorSelect.value) {
                alert('Please select a doctor!');
                e.preventDefault();
                return;
            }
            
            if (!dateSelect.value) {
                alert('Please select a date!');
                e.preventDefault();
                return;
            }
            
            if (!timeSelect.value) {
                alert('Please select a time!');
                e.preventDefault();
                return;
            }
            
            if (!problemText.value.trim()) {
                alert('Please describe your problem!');
                e.preventDefault();
                return;
            }
            
            // Show loading spinner
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            submitBtn.querySelector('i').style.display = 'none';
        });

        // Update preview on input change
        document.getElementById('doctorSelect').addEventListener('change', updatePreview);
        document.getElementById('dateSelect').addEventListener('change', updatePreview);
        document.getElementById('timeSelect').addEventListener('change', updatePreview);
        document.getElementById('problemText').addEventListener('input', updatePreview);

        // Initialize with default values
        document.addEventListener('DOMContentLoaded', function() {
            // Set default values
            if (document.getElementById('doctorSelect').options.length > 1) {
                document.getElementById('doctorSelect').selectedIndex = 1;
            }
            
            if (document.getElementById('dateSelect').options.length > 1) {
                document.getElementById('dateSelect').selectedIndex = 1;
            }
            
            if (document.getElementById('timeSelect').options.length > 1) {
                document.getElementById('timeSelect').selectedIndex = 1;
            }
            
            updatePreview();
            
            // Check for success message and show animation
            <?php if($success): ?>
                const successMessage = `<?php echo addslashes($msg); ?>`;
                document.getElementById('bookingForm').style.display = 'none';
                document.getElementById('successAnimation').style.display = 'block';
                document.getElementById('successMessage').textContent = successMessage;
            <?php endif; ?>
        });
    </script>
</body>
</html>

<?php mysqli_close($con); ?>