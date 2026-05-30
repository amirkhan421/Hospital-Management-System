<?php
// Database connection
$con = mysqli_connect('localhost', 'root', '', 'Clinic');

// Check connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission
if (isset($_POST["send"])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    $insert = "INSERT INTO `home_contact` (`name`, `email`, `message`) VALUES ('$name','$email','$message')";
    $query = mysqli_query($con, $insert);

    if ($query) {
        echo "<script>alert('Successfully sent message!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital Management System | Home</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: "Poppins", sans-serif;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 123, 255, 0.8), rgba(0, 123, 255, 0.8)),
                url('https://img.freepik.com/free-photo/hospital-corridor-with-empty-bed_23-2149151015.jpg') center/cover no-repeat;
            height: 80vh;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            font-size: 3rem;
        }

        .hero p {
            font-size: 1.2rem;
        }

        /* Cards */
        .card {
            border-radius: 15px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        /* Service boxes */
        .service-box {
            transition: 0.3s ease;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 2rem 1rem;
        }

        .service-box:hover {
            transform: translateY(-8px);
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        .service-box i {
            transition: 0.3s ease;
            margin-bottom: 1rem;
        }

        .service-box:hover i {
            color: #fff !important;
            transform: scale(1.2);
        }

        /* Navbar */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Sections */
        section {
            padding: 5rem 0;
        }

        /* Footer */
        footer {
            font-size: 0.9rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }

        /* Form styling */
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="fa-solid fa-hospital me-2"></i>MediCare Hospital
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active text-primary fw-semibold" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/video-call/Choose_page_login_option.php">Video Call</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/Doctor/Doctorlogin.php">Doctor Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/Patient/Patientlogin.php">Patient Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/adminLogin.php">Coordinator</a></li>                    
                    <li class="nav-item"><a class="btn btn-primary ms-3 px-4" href="admin/Patient/Patientlogin.php/login.php"><i class="fa-solid fa-user me-2"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero text-center text-white mt-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Welcome to MediCare Hospital</h1>
            <p class="lead mb-4">Advanced Healthcare Management System for Better Patient Care and Efficient Hospital Operations</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="../Clinic/admin/Patient/PatientRegistration.php" class="btn btn-light btn-lg px-4 py-3 fw-semibold">
                    <i class="fa-solid fa-calendar-check me-2"></i>Book Appointment
                </a>
                <a href="#services" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
                    <i class="fa-solid fa-stethoscope me-2"></i>Our Services
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="fw-bold text-primary mb-4">About Our Hospital</h2>
            <p class="text-muted mb-5 lead">We provide comprehensive healthcare services with state-of-the-art technology and experienced medical professionals.</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-4 h-100">
                        <i class="fa-solid fa-user-doctor fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">Expert Doctors</h5>
                        <p class="text-muted">Highly qualified and experienced medical professionals across all specialties.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm p-4 h-100">
                        <i class="fa-solid fa-bed-pulse fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">Patient Care</h5>
                        <p class="text-muted">Comprehensive patient care with modern facilities and personalized treatment plans.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm p-4 h-100">
                        <i class="fa-solid fa-calendar-check fa-3x text-warning mb-3"></i>
                        <h5 class="fw-bold">Easy Appointments</h5>
                        <p class="text-muted">Simple and convenient appointment booking system for better patient experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold text-primary mb-4">Our Medical Services</h2>
            <p class="text-muted mb-5">Comprehensive healthcare services for all your medical needs</p>

            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-heart-pulse fa-3x text-primary"></i>
                        <h6 class="fw-bold">Cardiology</h6>
                        <small>Heart care and treatment</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-brain fa-3x text-primary"></i>
                        <h6 class="fw-bold">Neurology</h6>
                        <small>Brain and nerve care</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-baby fa-3x text-primary"></i>
                        <h6 class="fw-bold">Pediatrics</h6>
                        <small>Child healthcare</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-x-ray fa-3x text-primary"></i>
                        <h6 class="fw-bold">Radiology</h6>
                        <small>Advanced imaging</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-bone fa-3x text-primary"></i>
                        <h6 class="fw-bold">Orthopedics</h6>
                        <small>Bone and joint care</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-eye fa-3x text-primary"></i>
                        <h6 class="fw-bold">Ophthalmology</h6>
                        <small>Eye care and surgery</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-tooth fa-3x text-primary"></i>
                        <h6 class="fw-bold">Dentistry</h6>
                        <small>Dental care</small>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="service-box text-center">
                        <i class="fa-solid fa-stethoscope fa-3x text-primary"></i>
                        <h6 class="fw-bold">Emergency</h6>
                        <small>24/7 emergency care</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-primary mb-3">Contact Us</h2>
                <p class="text-muted">Get in touch with us for any queries or appointments</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow border-0">
                        <div class="card-body p-5">
                            <form action="Home.php" method="POST">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Your Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Your Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Your Message</label>
                                        <textarea class="form-control" name="message" rows="5" placeholder="Tell us about your query or appointment request..." required></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" name="send" class="btn btn-primary px-5 py-3">
                                            <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fa-solid fa-hospital me-2"></i>MediCare Hospital</h5>
                    <p class="mb-0">Providing quality healthcare services since 2005.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fa-solid fa-phone me-2"></i>+92 300 1234567<br>
                        <i class="fa-solid fa-envelope me-2"></i>info@medicare.com
                    </p>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2025 MediCare Hospital Management System | All Rights Reserved</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>