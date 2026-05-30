<?php
if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $blood_group = $_POST['blood_group'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    $con = mysqli_connect('localhost', 'root', '', 'Clinic');
    $insert = "INSERT INTO `patient_register_login`(`Full_Name`, `Username`, `Email`, `Phone`, `Gender`, `Age`, `Blood_Group`, `Address`, `Password`) VALUES ('$fullname','$username','$email','$phone','$gender','$age','$blood_group','$address','$password')";
    $result = mysqli_query($con, $insert);

    if ($result) {
        echo "Successfully Form Filled";
        header("Location: Patientlogin.php");
        exit();
    } else {
        echo "Invalid form submission!";
    }
}
?> 



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register Patient | Hospital Management</title>

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
        }
    </style>
</head>

<body>
    <div class="card card-register d-flex flex-row">
        <!-- Left descriptive panel -->
        <div class="left-panel col-md-4 d-none d-md-flex">
            <div class="brand-badge"><i class="fa-solid fa-hospital-user"></i></div>
            <h2>Patient Registration</h2>
            <p>Register as a patient to book appointments and consult with doctors easily and securely.</p>
            <div class="small-muted text-center">Fast • Secure • Reliable</div>
        </div>

        <!-- Form panel -->
        <div class="form-panel col-md-8">
            <form action="PatientRegistration.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- name & username -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fa fa-user"></i>
                            <input type="text" name="fullname" class="form-control" placeholder="Full name" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Username</label>
                        <div class="input-with-icon">
                            <i class="fa fa-id-badge"></i>
                            <input type="text" name="username" class="form-control" placeholder="Unique username" required>
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

                    <!-- gender & age -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Gender</label>
                        <div class="input-with-icon">
                            <i class="fa fa-venus-mars"></i>
                            <select name="gender" class="form-select" required>
                                <option value="">Select gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Age</label>
                        <div class="input-with-icon">
                            <i class="fa fa-calendar"></i>
                            <input type="number" name="age" class="form-control" placeholder="Enter your age" required>
                        </div>
                    </div>

                    <!-- blood group & address -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Blood Group</label>
                        <div class="input-with-icon">
                            <i class="fa fa-heart"></i>
                            <select name="blood_group" class="form-select" required>
                                <option value="">Select blood group</option>
                                <option>A+</option>
                                <option>A-</option>
                                <option>B+</option>
                                <option>B-</option>
                                <option>AB+</option>
                                <option>AB-</option>
                                <option>O+</option>
                                <option>O-</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small-muted">Address</label>
                        <div class="input-with-icon">
                            <i class="fa fa-location-dot"></i>
                            <input type="text" name="address" class="form-control" placeholder="Enter your address">
                        </div>
                    </div>

                    <!-- passwords -->
                    <div class="col-md-6">
                        <label class="form-label small-muted">Password</label>
                        <div class="input-with-icon">
                            <i class="fa fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <!-- submit -->
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <div class="small-muted">Already have an account? <a href="Patientlogin.php">Login</a></div>
                        <button type="submit" name="submit" class="btn btn-primary-custom">
                            <i class="fa fa-user-plus me-2"></i> Register Patient
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>