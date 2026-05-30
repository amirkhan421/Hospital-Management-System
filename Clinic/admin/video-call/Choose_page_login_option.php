<?php
if(isset($_SESSION['logout_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['logout_message'] . "</div>";
    unset($_SESSION['logout_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Choose_page_login_Option</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body{
            background: linear-gradient(135deg,#1565c0,#0d47a1);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            position: relative;
        }

        /* Navigation Container */
        .nav-container {
            position: fixed;
            top: 25px;
            left: 25px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Home Button */
        .nav-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            color: #0d6efd !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            border: 3px solid #0d6efd;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25);
            animation: pulse 2s infinite;
            width: 180px;
        }

        .nav-btn:hover {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white !important;
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.5);
            text-decoration: none;
        }

        /* Admin Panel Button */
        .nav-btn.admin {
            border: 3px solid #dc3545;
            color: #dc3545 !important;
            animation: pulseAdmin 2s infinite;
        }

        .nav-btn.admin:hover {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white !important;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.5);
        }

        @keyframes pulse {
            0% { box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25); }
            50% { box-shadow: 0 6px 25px rgba(13, 110, 253, 0.4); }
            100% { box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25); }
        }

        @keyframes pulseAdmin {
            0% { box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25); }
            50% { box-shadow: 0 6px 25px rgba(220, 53, 69, 0.4); }
            100% { box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25); }
        }

        .box{
            background:white;
            padding:40px;
            border-radius:20px;
            width:100%;
            max-width:450px;
            text-align:center;
            box-shadow:0 15px 40px rgba(0,0,0,0.3);
            animation:fadeIn 1s ease;
            position: relative;
            z-index: 10;
        }

        .box h2{
            margin-bottom:25px;
            color:#0d47a1;
            font-weight: 700;
        }

        .option{
            border:2px solid #ddd;
            padding:20px;
            border-radius:15px;
            margin-bottom:15px;
            cursor:pointer;
            transition:0.3s;
            display: block;
        }

        .option:hover{
            border-color:#0d47a1;
            background:#f1f6ff;
            transform: translateY(-5px);
        }

        .option input{
            margin-right:10px;
            display: none;
        }

        .option i{
            font-size:35px;
            color:#0d47a1;
            margin-bottom:10px;
            display: block;
        }

        .option h5{
            margin: 10px 0 0 0;
            color: #333;
            font-weight: 600;
        }

        button{
            margin-top:20px;
            width:100%;
            padding:12px;
            border:none;
            border-radius:25px;
            background:#0d47a1;
            color:white;
            font-size:18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button:hover{
            background:#1565c0;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(13, 71, 161, 0.3);
        }

        @keyframes fadeIn{
            from{opacity:0; transform:translateY(30px);}
            to{opacity:1; transform:translateY(0);}
        }

        /* Floating Shapes Background */
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation: float1 20s infinite linear;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation: float2 25s infinite linear;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation: float3 30s infinite linear;
        }

        @keyframes float1 {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        @keyframes float2 {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(30px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        @keyframes float3 {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                top: 15px;
                left: 15px;
                right: 15px;
                flex-direction: row;
                justify-content: space-between;
            }
            
            .nav-btn {
                width: auto;
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .box {
                margin: 80px 20px 20px;
                padding: 30px 20px;
            }
        }

        @media (max-width: 480px) {
            .nav-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .nav-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Navigation Buttons -->
    <div class="nav-container">
        <a href="/Clinic/Home.php" class="nav-btn">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        
        <a href="../video-call/admin/adminlogincall.php" class="nav-btn admin">
            <i class="fas fa-user-shield"></i>
            <span>Control</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="box">
        <h2><i class="fas fa-sign-in-alt me-2"></i>Select Login Type</h2>

        <form id="loginForm">
            <!-- Patient Login Option -->
            <label class="option" for="patient">
                <input type="radio" name="user_type" value="patient" id="patient" required>
                <i class="fas fa-user-injured"></i>
                <h5>Patient Login</h5>
                <small class="text-muted">Book appointments and view medical history</small>
            </label>

            <!-- Doctor Login Option -->
            <label class="option" for="doctor">
                <input type="radio" name="user_type" value="doctor" id="doctor" required>
                <i class="fas fa-user-md"></i>
                <h5>Doctor Login</h5>
                <small class="text-muted">Manage appointments and patient consultations</small>
            </label>

            <button type="submit">
                <i class="fas fa-arrow-right me-2"></i>Continue
            </button>
        </form>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e){
            e.preventDefault();

            let userType = document.querySelector('input[name="user_type"]:checked');

            if(!userType){
                alert("Please select login type");
                return;
            }

            // Add loading animation to button
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redirecting...';
            button.disabled = true;

            // Redirect after short delay
            setTimeout(() => {
                if(userType.value === "patient"){
                    window.location.href = "../video-call/patientcall/patient_login_call.php";
                }else{
                    window.location.href = "../video-call/doctorcall/doctor_login_call.php";
                }
            }, 1000);
        });

        // Add click effect to options
        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.option').forEach(opt => {
                    opt.style.borderColor = '#ddd';
                    opt.style.transform = 'translateY(0)';
                });
                
                // Add selected effect to clicked option
                this.style.borderColor = '#0d47a1';
                this.style.transform = 'translateY(-5px)';
            });
        });

        // Auto-select first option on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstOption = document.querySelector('.option');
            if(firstOption) {
                const radio = firstOption.querySelector('input');
                if(radio) {
                    radio.checked = true;
                    firstOption.style.borderColor = '#0d47a1';
                    firstOption.style.transform = 'translateY(-5px)';
                }
            }
        });
    </script>

</body>
</html>