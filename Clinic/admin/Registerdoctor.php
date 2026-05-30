<?php
$con = mysqli_connect("localhost", "root", "", "Clinic");
if (!$con) die("Connection failed: " . mysqli_connect_error());

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $specialization = $_POST['specialization'];
    $license = $_POST['license'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO register_doctor
        (Full_Name, Username, Email, Phone, Specialization, License_Number, Password)
        VALUES ('$fullname', '$username', '$email', '$phone', '$specialization', '$license', '$hashed_password')";

        $running = mysqli_query($con, $query);

        if ($running) {
            echo "<script>
              alert('Doctor Registered Successfully!');
              window.location='Doctorlogin.php';
            </script>";
        } else {
            echo "<script>alert('Error: Could not register doctor');</script>";
        }
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

        .file-drop {
            border: 2px dashed rgba(0, 102, 255, 0.12);
            border-radius: 10px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all .18s;
            cursor: pointer;
        }

        .file-drop:hover {
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.04);
            transform: translateY(-3px);
        }

        .pic-preview {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            background: #f6f8ff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid rgba(6, 6, 6, 0.04);
        }

        .pic-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
    </style>
</head>

<body>

    <div class="card card-register d-flex flex-row">
        <!-- Left descriptive panel -->
        <div class="left-panel col-md-4 d-none d-md-flex">
            <div class="brand-badge"><i class="fa-solid fa-user-doctor"></i></div>
            <h2>Doctor Registration</h2>
            <p>Create a secure account to manage patients, appointments, prescriptions and more. Upload your ID and license for verification.</p>
            <div class="small-muted text-center">Secure • Verified • Easy</div>
        </div>

        <!-- Form panel -->
        <div class="form-panel col-md-8">
            <form action="Registerdoctor.php" method="POST" enctype="multipart/form-data" id="regForm" novalidate>
                <div class="row g-3">
                    <!-- top row: name & username -->
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

                    <!-- email & phone -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Email</label>
                        <div class="input-with-icon">
                            <i class="fa fa-envelope"></i>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Phone</label>
                        <div class="input-with-icon">
                            <i class="fa fa-phone"></i>
                            <input type="tel" name="phone" class="form-control" placeholder="+92 300 1234567">
                        </div>
                    </div>

                    <!-- specialization & license number -->
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

                    <!-- password fields -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Password</label>
                        <div class="input-with-icon">
                            <i class="fa fa-lock"></i>
                            <input id="pass" type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fa fa-lock"></i>
                            <input id="cpass" type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                        </div>
                        <div id="passMsg" class="small text-danger mt-1" style="display:none;">Passwords do not match</div>
                    </div>

                    <!-- submit -->
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
</body>

</html>