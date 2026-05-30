<?php
session_start();

// Database connection
$con = mysqli_connect("localhost", "root", "", "clinic");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";
$show_error_modal = false;

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query to check user
    $query = "SELECT id, full_name, password FROM patient_register_call WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Since passwords are stored in plain text in your table
        if ($password === $row['password']) {
            // Set session variables
            $_SESSION['patient_id'] = $row['id'];
            $_SESSION['username'] = $username;
            $_SESSION['patient_name'] = $row['full_name'];
            $_SESSION['is_logged_in'] = true;
            
            header("Location: patient_choose&profile.php");
            exit();
        } else {
            $error = "Invalid password!";
            $show_error_modal = true;
        }
    } else {
        $error = "Username not found!";
        $show_error_modal = true;
    }
}
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Login Call</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body {
    background: linear-gradient(135deg, #1565c0, #0d47a1);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 20px;
}

/* Home Button Styles */
.home-btn-container {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
}

.home-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #0d47a1 !important;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    border: 2px solid #0d47a1;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(13, 71, 161, 0.15);
}

.home-btn:hover {
    background: #0d47a1;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 71, 161, 0.25);
    text-decoration: none;
}
.login-box {
    background: white;
    padding: 40px;
    border-radius: 20px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    animation: slideUp 0.5s ease-out;
}
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.login-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #0d47a1;
    font-weight: 600;
}
.form-control:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.25);
}
.btn-primary {
    background: linear-gradient(to right, #0d47a1, #1565c0);
    border: none;
    padding: 12px;
    font-weight: 500;
    transition: all 0.3s;
}
.btn-primary:hover {
    background: linear-gradient(to right, #0b3d91, #1254a8);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
}
.register-link {
    text-align: center;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.register-link a {
    text-decoration: none;
    font-weight: 600;
    color: #0d47a1;
    transition: color 0.3s;
}
.register-link a:hover {
    text-decoration: underline;
    color: #1565c0;
}
.modal-content {
    border-radius: 15px;
    overflow: hidden;
}
.modal-header {
    background: linear-gradient(to right, #dc3545, #c82333);
    color: white;
    border-bottom: none;
}
.modal-body {
    padding: 30px;
    text-align: center;
}
.error-icon {
    font-size: 60px;
    color: #dc3545;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<!-- Home Button -->
<div class="home-btn-container">
    <a href="../Choose_page_login_option.php" class="home-btn">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
</div>

<div class="login-box">
    <h2><i class="bi bi-person-circle me-2"></i>Patient Login</h2>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-medium">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" required 
                       placeholder="Enter your username"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" required 
                       placeholder="Enter your password">
            </div>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100 mt-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
    </form>
    
    <div class="register-link">
        <p class="mb-2 text-muted">Don't have an account?</p>
        <a href="patient_registerl_call.php" class="d-inline-flex align-items-center">
            <i class="bi bi-person-plus me-2"></i>Create New Account
        </a>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Login Failed
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="error-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <h4 class="mb-3 text-danger">Login Error</h4>
                <p class="lead"><?php echo htmlspecialchars($error); ?></p>
                <p class="text-muted">Please check your credentials and try again.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Show error modal if there's an error
<?php if ($show_error_modal): ?>
document.addEventListener('DOMContentLoaded', function() {
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
});
<?php endif; ?>

// Focus on username field when modal is closed
document.getElementById('errorModal').addEventListener('hidden.bs.modal', function () {
    document.querySelector('input[name="username"]').focus();
});
</script>

</body>
</html>