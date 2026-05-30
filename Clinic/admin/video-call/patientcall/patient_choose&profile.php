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
            'display' => $nextSunday->format('d M, Y (l)'),
            'day' => $nextSunday->format('d'),
            'month' => $nextSunday->format('M')
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
            'display' => $display,
            'hour' => $hour,
            'period' => $hour < 12 ? 'AM' : 'PM'
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
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #2196f3;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --light-bg: #f0f8ff;
            --gradient-primary: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            --gradient-success: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
            --gradient-warning: linear-gradient(135deg, #f57c00 0%, #ff9800 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
            background: rgba(33, 150, 243, 0.2);
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 5s;
            background: rgba(76, 175, 80, 0.15);
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 15%;
            animation-delay: 10s;
            background: rgba(255, 152, 0, 0.15);
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) rotate(90deg);
            }
            50% {
                transform: translateY(0) rotate(180deg);
            }
            75% {
                transform: translateY(20px) rotate(270deg);
            }
        }
        
        /* Pulse Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes slideInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }
        
        .booking-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 1s ease-out;
        }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, 
                #0d47a1, #2196f3, #4caf50, #ff9800);
            background-size: 400% 100%;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .booking-header h3 {
            color: var(--primary-color);
            font-weight: 800;
            margin-bottom: 15px;
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .booking-header h3::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
            animation: widthGrow 1.5s ease-out;
        }
        
        @keyframes widthGrow {
            from { width: 0; }
            to { width: 100px; }
        }
        
        .booking-header p {
            color: #666;
            font-size: 1.1rem;
            margin-top: 25px;
        }
        
        .patient-info-box {
            background: linear-gradient(135deg, rgba(227, 242, 253, 0.9), rgba(187, 222, 251, 0.9));
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
            border-left: 6px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 25px;
            box-shadow: 0 10px 30px rgba(33, 150, 243, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 1s ease 0.3s both;
        }
        
        .patient-info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(33, 150, 243, 0.25);
        }
        
        .patient-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.3);
            animation: pulse 2s infinite;
        }
        
        .form-step {
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-step:nth-child(1) { animation-delay: 0.1s; }
        .form-step:nth-child(2) { animation-delay: 0.2s; }
        .form-step:nth-child(3) { animation-delay: 0.3s; }
        .form-step:nth-child(4) { animation-delay: 0.4s; }
        
        .step-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }
        
        .step-title h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
        }
        
        /* Doctor Cards */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .doctor-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 3px solid transparent;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .doctor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary-color);
        }
        
        .doctor-card:hover::before {
            transform: scaleX(1);
        }
        
        .doctor-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #f0f9ff, #e3f2fd);
            transform: translateY(-5px);
            animation: selectPulse 0.5s ease;
        }
        
        .doctor-card.selected::before {
            transform: scaleX(1);
        }
        
        @keyframes selectPulse {
            0%, 100% { transform: translateY(-5px); }
            50% { transform: translateY(-8px); }
        }
        
        .doctor-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .doctor-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.3);
        }
        
        .doctor-info h6 {
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }
        
        .doctor-info .specialization {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            background: rgba(33, 150, 243, 0.1);
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .doctor-details {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .detail-item i {
            color: var(--primary-color);
        }
        
        /* Date Cards */
        .dates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .date-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 3px solid transparent;
            transition: all 0.4s ease;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }
        
        .date-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .date-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary-color);
        }
        
        .date-card:hover::before {
            transform: scaleX(1);
        }
        
        .date-card.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #f0f9ff, #e3f2fd);
            transform: translateY(-5px);
        }
        
        .date-card.selected::before {
            transform: scaleX(1);
        }
        
        .date-day {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .date-month {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .date-year {
            font-size: 1rem;
            color: #666;
            margin-bottom: 10px;
        }
        
        .date-weekday {
            font-size: 0.9rem;
            color: var(--secondary-color);
            font-weight: 600;
            background: rgba(33, 150, 243, 0.1);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }
        
        /* Time Slots */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .time-slot {
            background: white;
            border-radius: 15px;
            padding: 20px 10px;
            text-align: center;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .time-slot:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary-color);
        }
        
        .time-slot.selected {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-5px);
            animation: timeSelect 0.3s ease;
        }
        
        @keyframes timeSelect {
            0% { transform: translateY(-5px) scale(1); }
            50% { transform: translateY(-5px) scale(1.05); }
            100% { transform: translateY(-5px) scale(1); }
        }
        
        .time-value {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .time-period {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Problem Input */
        .problem-box {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        
        .problem-box:focus-within {
            border-color: var(--secondary-color);
            box-shadow: 0 15px 40px rgba(33, 150, 243, 0.2);
            transform: translateY(-5px);
        }
        
        .problem-box textarea {
            border: none;
            outline: none;
            width: 100%;
            min-height: 150px;
            font-size: 1rem;
            line-height: 1.6;
            resize: vertical;
            background: transparent;
        }
        
        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 20px;
            margin-top: 40px;
        }
        
        .btn-preview {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            flex: 1;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
        }
        
        .btn-preview:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(108, 117, 125, 0.4);
            background: linear-gradient(135deg, #5a6268, #343a40);
        }
        
        .btn-book {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            flex: 1;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(13, 71, 161, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-book::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-book:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(13, 71, 161, 0.4);
            background: linear-gradient(135deg, #0b3d91, #1e88e5);
        }
        
        .btn-book:hover::before {
            left: 100%;
        }
        
        /* Success Animation */
        .success-animation {
            text-align: center;
            padding: 60px 40px;
            display: none;
            animation: fadeIn 0.8s ease;
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--success-color), #66bb6a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: bounceIn 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 20px 40px rgba(76, 175, 80, 0.3);
        }
        
        .success-icon i {
            font-size: 60px;
            color: white;
        }
        
        .success-animation h4 {
            color: var(--success-color);
            font-weight: 800;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .success-animation p {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        /* Alert */
        .alert-animated {
            animation: slideInDown 0.5s ease, shake 0.5s ease 0.5s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        /* Preview Section */
        .appointment-preview {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 20px;
            padding: 30px;
            margin-top: 40px;
            border: 3px dashed var(--primary-color);
            display: none;
            animation: fadeIn 0.8s ease;
        }
        
        .preview-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .preview-header h5 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .preview-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .preview-card:hover {
            transform: translateY(-5px);
        }
        
        .preview-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .preview-value {
            color: #333;
            font-weight: 700;
            font-size: 1.2rem;
            line-height: 1.4;
        }
        
        /* Progress Bar */
        .progress-container {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            position: relative;
        }
        
        .progress-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e0e0e0;
            transform: translateY(-50%);
            z-index: 1;
        }
        
        .progress-bar {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: translateY(-50%);
            transition: width 0.4s ease;
            z-index: 2;
        }
        
        .step-indicator {
            width: 50px;
            height: 50px;
            background: white;
            border: 4px solid #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
            position: relative;
            z-index: 3;
            transition: all 0.4s ease;
        }
        
        .step-indicator.active {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 8px rgba(33, 150, 243, 0.2);
        }
        
        .step-label {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .step-indicator.active .step-label {
            color: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .booking-card {
                padding: 30px;
                margin: 20px;
            }
            
            .doctors-grid {
                grid-template-columns: 1fr;
            }
            
            .dates-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .booking-header h3 {
                font-size: 1.8rem;
            }
            
            .dates-grid {
                grid-template-columns: 1fr;
            }
            
            .time-slots-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .patient-info-box {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .preview-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            width: 80px;
            height: 80px;
            border: 8px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--secondary-color);
            animation: spin 1s linear infinite;
        }
        
        /* Hidden Inputs */
        input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="booking-container">
        <div class="booking-card">
            <!-- Success animation -->
            <div class="success-animation" id="successAnimation">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h4>Appointment Booked Successfully!</h4>
                <p id="successMessage"></p>
                <button class="btn btn-primary btn-lg" onclick="window.location.href='patient_choose&profile.php'">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </button>
            </div>
            
            <!-- Main form -->
            <div id="bookingForm">
                <!-- Header -->
                <div class="booking-header">
                    <h3><i class="fas fa-calendar-check me-3"></i>Book Your Appointment</h3>
                    <p class="text-muted">Schedule your consultation with our expert doctors in just a few clicks</p>
                </div>
                
                <!-- Patient Info -->
                <div class="patient-info-box">
                    <div class="patient-avatar">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-2"><?php echo htmlspecialchars($_SESSION['patient_name']); ?></h5>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-id-card me-2"></i>Patient ID: <?php echo $_SESSION['patient_id']; ?>
                        </p>
                    </div>
                    <a href="patient_choose&profile.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </div>
                
                <!-- Message Alert -->
                <?php if($msg): ?>
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-animated alert-dismissible fade show" role="alert">
                        <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                        <?php echo htmlspecialchars($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar"></div>
                    <?php $steps = ['Doctor', 'Date', 'Time', 'Details']; ?>
                    <?php foreach($steps as $index => $step): ?>
                        <div class="step-indicator <?php echo $index === 0 ? 'active' : ''; ?>" id="step<?php echo $index + 1; ?>">
                            <?php echo $index + 1; ?>
                            <span class="step-label"><?php echo $step; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Booking Form -->
                <form method="POST" id="appointmentForm">
                    <!-- Step 1: Doctor Selection -->
                    <div class="form-step" id="step1Content">
                        <div class="step-title">
                            <div class="step-number">1</div>
                            <h5><i class="fas fa-user-md me-2"></i>Select Your Doctor</h5>
                        </div>
                        
                        <div class="doctors-grid">
                            <?php 
                            $doctor_index = 0;
                            while($doc = mysqli_fetch_assoc($doctors_result)): 
                            ?>
                                <div class="doctor-card" onclick="selectDoctor(<?php echo $doc['id']; ?>)">
                                    <div class="doctor-header">
                                        <div class="doctor-avatar">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div class="doctor-info">
                                            <h6>Dr. <?php echo htmlspecialchars($doc['full_name']); ?></h6>
                                            <span class="specialization"><?php echo htmlspecialchars($doc['specialization']); ?></span>
                                        </div>
                                    </div>
                                    <div class="doctor-details">
                                        <?php if(isset($doc['license_number'])): ?>
                                            <div class="detail-item">
                                                <i class="fas fa-id-card"></i>
                                                <span><?php echo htmlspecialchars($doc['license_number']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="detail-item">
                                            <i class="fas fa-star"></i>
                                            <span>4.8 Rating</span>
                                        </div>
                                    </div>
                                    <input type="radio" name="doctor_id" value="<?php echo $doc['id']; ?>" 
                                           id="doctor_<?php echo $doc['id']; ?>" required
                                           <?php echo $doctor_index === 0 ? 'checked' : ''; ?>>
                                </div>
                            <?php 
                            $doctor_index++;
                            endwhile; 
                            ?>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-primary btn-lg" onclick="nextStep(2)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Date Selection -->
                    <div class="form-step" id="step2Content" style="display: none;">
                        <div class="step-title">
                            <div class="step-number">2</div>
                            <h5><i class="fas fa-calendar-alt me-2"></i>Select Appointment Date</h5>
                        </div>
                        
                        <div class="dates-grid">
                            <?php foreach($availableSundays as $index => $sunday): ?>
                                <div class="date-card" onclick="selectDate('<?php echo $sunday['date']; ?>')">
                                    <div class="date-day"><?php echo $sunday['day']; ?></div>
                                    <div class="date-month"><?php echo $sunday['month']; ?></div>
                                    <div class="date-year"><?php echo date('Y', strtotime($sunday['date'])); ?></div>
                                    <div class="date-weekday">Sunday</div>
                                    <input type="radio" name="date" value="<?php echo $sunday['date']; ?>" 
                                           id="date_<?php echo $sunday['date']; ?>" required
                                           <?php echo $index === 0 ? 'checked' : ''; ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="info-note mt-4">
                            <i class="fas fa-info-circle fa-2x"></i>
                            <div>
                                <strong>Note:</strong> Appointments are only available on Sundays. Each appointment slot is 30 minutes.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="prevStep(1)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" onclick="nextStep(3)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Time Selection -->
                    <div class="form-step" id="step3Content" style="display: none;">
                        <div class="step-title">
                            <div class="step-number">3</div>
                            <h5><i class="fas fa-clock me-2"></i>Select Time Slot</h5>
                        </div>
                        
                        <div class="time-slots-grid">
                            <?php foreach($timeSlots as $index => $slot): ?>
                                <div class="time-slot" onclick="selectTime('<?php echo $slot['time']; ?>')">
                                    <div class="time-value"><?php echo date('h:i', strtotime($slot['time'])); ?></div>
                                    <div class="time-period"><?php echo $slot['period']; ?></div>
                                    <input type="radio" name="time" value="<?php echo $slot['time']; ?>" 
                                           id="time_<?php echo $slot['time']; ?>" required
                                           <?php echo $index === 2 ? 'checked' : ''; ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="prevStep(2)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" onclick="nextStep(4)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 4: Problem Details -->
                    <div class="form-step" id="step4Content" style="display: none;">
                        <div class="step-title">
                            <div class="step-number">4</div>
                            <h5><i class="fas fa-file-medical me-2"></i>Describe Your Problem</h5>
                        </div>
                        
                        <div class="problem-box">
                            <textarea name="problem" id="problemText" 
                                      placeholder="Please describe your symptoms or medical concern in detail...
Example: I've been experiencing severe headaches for the past week, accompanied by dizziness and blurred vision. The pain is mostly on the right side and worsens in the evenings..." 
                                      required><?php echo isset($_POST['problem']) ? htmlspecialchars($_POST['problem']) : ''; ?></textarea>
                        </div>
                        
                        <div class="appointment-preview" id="appointmentPreview">
                            <div class="preview-header">
                                <h5><i class="fas fa-file-alt me-2"></i>Appointment Preview</h5>
                            </div>
                            <div class="preview-grid">
                                <div class="preview-card">
                                    <div class="preview-label">
                                        <i class="fas fa-user-md"></i>Doctor
                                    </div>
                                    <div class="preview-value" id="previewDoctor"></div>
                                </div>
                                <div class="preview-card">
                                    <div class="preview-label">
                                        <i class="fas fa-calendar-alt"></i>Date
                                    </div>
                                    <div class="preview-value" id="previewDate"></div>
                                </div>
                                <div class="preview-card">
                                    <div class="preview-label">
                                        <i class="fas fa-clock"></i>Time
                                    </div>
                                    <div class="preview-value" id="previewTime"></div>
                                </div>
                                <div class="preview-card">
                                    <div class="preview-label">
                                        <i class="fas fa-stethoscope"></i>Problem
                                    </div>
                                    <div class="preview-value" id="previewProblem"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="prevStep(3)">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <div class="action-buttons">
                                <button type="button" class="btn-preview" onclick="showPreview()">
                                    <i class="fas fa-eye"></i>Preview Appointment
                                </button>
                                <button type="submit" class="btn-book" id="submitBtn">
                                    <i class="fas fa-calendar-plus"></i>Book Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Step Navigation
        let currentStep = 1;
        const totalSteps = 4;
        
        function nextStep(step) {
            // Validate current step
            if (step === 2 && !validateStep1()) return;
            if (step === 3 && !validateStep2()) return;
            if (step === 4 && !validateStep3()) return;
            
            // Hide current step
            document.getElementById(`step${currentStep}Content`).style.display = 'none';
            document.getElementById(`step${currentStep}`).classList.remove('active');
            
            // Show next step
            currentStep = step;
            document.getElementById(`step${currentStep}Content`).style.display = 'block';
            document.getElementById(`step${currentStep}`).classList.add('active');
            
            // Update progress bar
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
            
            // Animate step transition
            const stepContent = document.getElementById(`step${step}Content`);
            stepContent.style.animation = 'none';
            setTimeout(() => {
                stepContent.style.animation = 'fadeInUp 0.8s ease forwards';
            }, 10);
            
            // Update preview if on last step
            if (step === 4) {
                updatePreview();
            }
        }
        
        function prevStep(step) {
            // Hide current step
            document.getElementById(`step${currentStep}Content`).style.display = 'none';
            document.getElementById(`step${currentStep}`).classList.remove('active');
            
            // Show previous step
            currentStep = step;
            document.getElementById(`step${currentStep}Content`).style.display = 'block';
            document.getElementById(`step${currentStep}`).classList.add('active');
            
            // Update progress bar
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
            
            // Animate step transition
            const stepContent = document.getElementById(`step${step}Content`);
            stepContent.style.animation = 'none';
            setTimeout(() => {
                stepContent.style.animation = 'fadeInUp 0.8s ease forwards';
            }, 10);
        }
        
        // Step Validations
        function validateStep1() {
            const doctorSelected = document.querySelector('input[name="doctor_id"]:checked');
            if (!doctorSelected) {
                showError('Please select a doctor to continue!');
                return false;
            }
            return true;
        }
        
        function validateStep2() {
            const dateSelected = document.querySelector('input[name="date"]:checked');
            if (!dateSelected) {
                showError('Please select a date to continue!');
                return false;
            }
            return true;
        }
        
        function validateStep3() {
            const timeSelected = document.querySelector('input[name="time"]:checked');
            if (!timeSelected) {
                showError('Please select a time slot to continue!');
                return false;
            }
            return true;
        }
        
        function showError(message) {
            // Create error alert
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-animated alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            // Insert after patient info box
            const patientBox = document.querySelector('.patient-info-box');
            patientBox.parentNode.insertBefore(alert, patientBox.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
        
        // Selection Functions
        function selectDoctor(doctorId) {
            // Remove selected class from all doctor cards
            document.querySelectorAll('.doctor-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            const selectedCard = document.querySelector(`.doctor-card input[value="${doctorId}"]`).parentElement;
            selectedCard.classList.add('selected');
            
            // Check the radio button
            document.querySelector(`input[value="${doctorId}"]`).checked = true;
            
            // Add animation
            selectedCard.style.animation = 'selectPulse 0.5s ease';
            setTimeout(() => {
                selectedCard.style.animation = '';
            }, 500);
        }
        
        function selectDate(date) {
            // Remove selected class from all date cards
            document.querySelectorAll('.date-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            const selectedCard = document.querySelector(`.date-card input[value="${date}"]`).parentElement;
            selectedCard.classList.add('selected');
            
            // Check the radio button
            document.querySelector(`input[value="${date}"]`).checked = true;
        }
        
        function selectTime(time) {
            // Remove selected class from all time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            
            // Add selected class to clicked slot
            const selectedSlot = document.querySelector(`.time-slot input[value="${time}"]`).parentElement;
            selectedSlot.classList.add('selected');
            
            // Check the radio button
            document.querySelector(`input[value="${time}"]`).checked = true;
            
            // Add animation
            selectedSlot.style.animation = 'timeSelect 0.3s ease';
            setTimeout(() => {
                selectedSlot.style.animation = '';
            }, 300);
        }
        
        // Update Preview
        function updatePreview() {
            // Get selected values
            const doctorSelect = document.querySelector('input[name="doctor_id"]:checked');
            const dateSelect = document.querySelector('input[name="date"]:checked');
            const timeSelect = document.querySelector('input[name="time"]:checked');
            const problemText = document.getElementById('problemText');
            
            // Get doctor name from card
            let doctorName = 'Not selected';
            if (doctorSelect) {
                const doctorCard = doctorSelect.parentElement;
                doctorName = doctorCard.querySelector('h6').textContent;
            }
            
            // Format date
            let dateDisplay = 'Not selected';
            if (dateSelect) {
                const dateValue = dateSelect.value;
                const dateObj = new Date(dateValue);
                dateDisplay = dateObj.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
            
            // Format time
            let timeDisplay = 'Not selected';
            if (timeSelect) {
                const timeValue = timeSelect.value;
                const [hours, minutes] = timeValue.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                timeDisplay = `${displayHour}:${minutes} ${ampm}`;
            }
            
            // Update preview
            document.getElementById('previewDoctor').textContent = doctorName;
            document.getElementById('previewDate').textContent = dateDisplay;
            document.getElementById('previewTime').textContent = timeDisplay;
            document.getElementById('previewProblem').textContent = 
                problemText.value.substring(0, 100) + (problemText.value.length > 100 ? '...' : '');
        }
        
        // Show Preview
        function showPreview() {
            // Validate step 4
            if (!problemText.value.trim()) {
                showError('Please describe your problem before previewing!');
                problemText.focus();
                return;
            }
            
            updatePreview();
            const preview = document.getElementById('appointmentPreview');
            preview.style.display = 'block';
            
            // Animate preview
            preview.style.animation = 'none';
            setTimeout(() => {
                preview.style.animation = 'fadeIn 0.8s ease';
            }, 10);
            
            // Scroll to preview
            preview.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Form Submission
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all fields
            if (!validateStep1() || !validateStep2() || !validateStep3()) {
                showError('Please complete all steps before submitting!');
                return;
            }
            
            if (!problemText.value.trim()) {
                showError('Please describe your problem!');
                problemText.focus();
                return;
            }
            
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Submit form after delay for animation
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Select first items by default
            const firstDoctor = document.querySelector('.doctor-card');
            if (firstDoctor) {
                const doctorId = firstDoctor.querySelector('input').value;
                selectDoctor(doctorId);
            }
            
            const firstDate = document.querySelector('.date-card');
            if (firstDate) {
                const dateValue = firstDate.querySelector('input').value;
                selectDate(dateValue);
            }
            
            const defaultTime = document.querySelector('input[value="10:00"]');
            if (defaultTime) {
                selectTime('10:00');
            }
            
            // Check for success message
            <?php if($success): ?>
                const successMessage = `<?php echo addslashes($msg); ?>`;
                document.getElementById('bookingForm').style.display = 'none';
                document.getElementById('successAnimation').style.display = 'block';
                document.getElementById('successMessage').textContent = successMessage;
            <?php endif; ?>
            
            // Initialize animations
            initializeAnimations();
        });
        
        function initializeAnimations() {
            // Add hover effects
            const cards = document.querySelectorAll('.doctor-card, .date-card, .time-slot');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('selected')) {
                        this.style.transform = 'translateY(-8px)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('selected')) {
                        this.style.transform = 'translateY(0)';
                    } else {
                        this.style.transform = 'translateY(-5px)';
                    }
                });
            });
        }
    </script>
    <a href="http://localhost/clinic/admin/video-call/patientcall/show_awc.php#" class="btn btn-primary" target="_blank">
    <i class="fas fa-video me-2"></i> Join Video Call
</a>
</body>
</html>

<?php mysqli_close($con); ?>