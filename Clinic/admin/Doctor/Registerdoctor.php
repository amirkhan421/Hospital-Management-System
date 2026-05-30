<?php
$con = mysqli_connect("localhost", "root", "", "Clinic");
if (!$con) die("Connection failed: " . mysqli_connect_error());

if (isset($_POST['submit'])) {
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $specialization = mysqli_real_escape_string($con, $_POST['specialization']);
    $license = mysqli_real_escape_string($con, $_POST['license']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if username already exists
    $check_query = "SELECT * FROM register_doctor WHERE Username = '$username' OR Email = '$email'";
    $check_result = mysqli_query($con, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username or Email already exists!');</script>";
    } 
    elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } 
    elseif (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long!');</script>";
    }
    else {
        // Store password as plain text (NOT SECURE - for educational purposes only)
        $plain_password = $password; // No hashing!
        
        // Use prepared statement to prevent SQL injection
        $query = "INSERT INTO register_doctor (Full_Name, Username, Email, Phone, Specialization, License_Number, Password) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssssss", $fullname, $username, $email, $phone, $specialization, $license, $plain_password);
        
        $running = mysqli_stmt_execute($stmt);

        if ($running) {
            echo "<script>
              alert('Doctor Registered Successfully!');
              window.location='Doctorlogin.php';
            </script>";
        } else {
            echo "<script>alert('Error: Could not register doctor. " . mysqli_error($con) . "');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Doctor | Hospital Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0066ff;
            --accent: #6f42c1;
            --muted: #6c757d;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.09), rgba(111, 66, 193, 0.06));
            padding: 40px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-register {
            width: 100%;
            max-width: 980px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(18, 38, 63, 0.12);
        }

        .left-panel {
            background: linear-gradient(180deg, rgba(0, 102, 255, 0.06), rgba(111, 66, 193, 0.04));
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 18px;
            align-items: center;
        }

        .brand-badge {
            width: 92px;
            height: 92px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff;
            font-size: 34px;
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.12);
        }

        .left-panel h2 {
            margin: 0;
            color: var(--primary);
            font-weight: 600;
        }

        .left-panel p {
            color: var(--muted);
            text-align: center;
            max-width: 220px;
        }

        .form-panel {
            padding: 28px 34px;
            background: #fff;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon .fa {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            opacity: .9;
            pointer-events: none;
        }

        .input-with-icon input,
        .input-with-icon select,
        .input-with-icon textarea {
            padding-left: 42px;
            border-radius: 10px;
            height: 46px;
        }

        .small-muted {
            color: var(--muted);
            font-size: .9rem;
        }

        .btn-primary-custom {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border: 0;
            box-shadow: 0 8px 30px rgba(111, 66, 193, 0.08);
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
        }

        @media (max-width:880px) {
            .card-register {
                margin: 24px;
            }
            .left-panel {
                padding: 20px;
            }
            .form-panel {
                padding: 20px;
            }
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #198754; }
        
    </style>
</head>

<body>

    <div class="card card-register d-flex flex-row">
        <!-- Left descriptive panel -->
        <div class="left-panel col-md-4 d-none d-md-flex">
            <div class="brand-badge"><i class="fa-solid fa-user-doctor"></i></div>
            <h2>Doctor Registration</h2>
            <p>Create a secure account to manage patients, appointments, prescriptions and more.</p>
            <div class="small-muted text-center">Secure • Verified • Easy</div>
            <div class="small-muted text-center mt-3">
            </div>
        </div>

        <!-- Form panel -->
        <div class="form-panel col-md-8">
            
            <form action="" method="POST" id="regForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small-muted">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fa fa-user"></i>
                            <input type="text" name="fullname" class="form-control" placeholder="Dr. Aisha Khan" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Username</label>
                        <div class="input-with-icon">
                            <i class="fa fa-id-badge"></i>
                            <input type="text" name="username" class="form-control" placeholder="username (unique)" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Email</label>
                        <div class="input-with-icon">
                            <i class="fa fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Phone</label>
                        <div class="input-with-icon">
                            <i class="fa fa-phone"></i>
                            <input type="tel" name="phone" class="form-control" placeholder="+92 300 1234567" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Specialization</label>
                        <div class="input-with-icon">
                            <i class="fa fa-stethoscope"></i>
                            <select name="specialization" class="form-select" required>
                                <option value="">Select specialization</option>
                                <option>General Physician</option>
                                <option>Cardiologist</option>
                                <option>Pediatrician</option>
                                <option>Orthopedic</option>
                                <option>Dermatologist</option>
                                <option>Neurologist</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">License Number</label>
                        <div class="input-with-icon">
                            <i class="fa fa-file-medical"></i>
                            <input type="text" name="license" class="form-control" placeholder="LIC-12345678" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Password</label>
                        <div class="input-with-icon">
                            <i class="fa fa-lock"></i>
                            <input id="pass" type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                        </div>
                        <div id="passwordStrength" class="password-strength"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fa fa-lock"></i>
                            <input id="cpass" type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                        </div>
                        <div id="passMsg" class="small text-danger mt-1" style="display:none;">Passwords do not match</div>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <div class="small-muted">Already have an account? <a href="Doctorlogin.php">Login</a></div>
                        <button type="submit" name="submit" class="btn btn-primary-custom" id="submitBtn">
                            <i class="fa fa-user-plus me-2"></i> Register Doctor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Real-time password strength checker and match validator
    const password = document.getElementById('pass');
    const confirmPassword = document.getElementById('cpass');
    const passMsg = document.getElementById('passMsg');
    const strengthDiv = document.getElementById('passwordStrength');
    
    function checkPasswordStrength(pwd) {
        if(pwd.length === 0) return '';
        if(pwd.length < 6) return '<span class="strength-weak">❌ Too short (min 6 chars)</span>';
        
        let strength = 0;
        if(pwd.length >= 8) strength++;
        if(pwd.match(/[a-z]+/)) strength++;
        if(pwd.match(/[A-Z]+/)) strength++;
        if(pwd.match(/[0-9]+/)) strength++;
        if(pwd.match(/[$@#&!]+/)) strength++;
        
        if(strength <= 2) return '<span class="strength-weak">⚠️ Weak password</span>';
        if(strength <= 4) return '<span class="strength-medium">⚡ Medium password</span>';
        return '<span class="strength-strong">✅ Strong password</span>';
    }
    
    password.addEventListener('input', function() {
        strengthDiv.innerHTML = checkPasswordStrength(this.value);
        if(confirmPassword.value.length > 0) {
            if(this.value === confirmPassword.value) {
                passMsg.style.display = 'none';
            } else {
                passMsg.style.display = 'block';
            }
        }
    });
    
    confirmPassword.addEventListener('input', function() {
        if(password.value === this.value) {
            passMsg.style.display = 'none';
        } else {
            passMsg.style.display = 'block';
        }
    });
    
    // Form validation
    document.getElementById('regForm').addEventListener('submit', function(e) {
        if(password.value !== confirmPassword.value) {
            e.preventDefault();
            passMsg.style.display = 'block';
            alert('Passwords do not match!');
        } else if(password.value.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
        }
    });
    </script>
</body>

</html>