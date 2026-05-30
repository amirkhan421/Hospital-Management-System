<?php
// Start session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: patient_login_call.php");
exit();
?>
