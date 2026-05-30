<?php
session_start();

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'Clinic');
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get appointment ID from URL
if (!isset($_GET['id'])) {
    header("Location: Viewappointments.php");
    exit();
}

$appointment_id = (int)$_GET['id'];

// Delete appointment
$delete = mysqli_query($con, "DELETE FROM appointments WHERE id=$appointment_id");
if ($delete) {
    header("Location: Viewappointments.php");
    exit();
} else {
    echo "Failed to delete appointment: " . mysqli_error($con);
}
