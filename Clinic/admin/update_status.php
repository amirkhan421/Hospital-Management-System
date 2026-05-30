<?php
session_start();

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'Clinic');
if (!$con) {
    die('Database Connection Failed: ' . mysqli_connect_error());
}

// Make sure appointment ID is provided
if(!isset($_GET['id'])) {
    header("Location: Viewappointments.php");
    exit();
}

$appointment_id = (int) $_GET['id'];

// Fetch appointment data
$result = mysqli_query($con, "SELECT * FROM `appointments` WHERE id = $appointment_id");
if(mysqli_num_rows($result) == 0) {
    echo "Appointment not found!";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($con, $_POST['status']);

    $update_query = "UPDATE `appointments` SET `status` = '$new_status' WHERE id = $appointment_id";
    if(mysqli_query($con, $update_query)) {
        header("Location: Viewappointments.php?updated=true");
        exit();
    } else {
        echo "Failed to update status: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Appointment Status</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Update Status for <?php echo htmlspecialchars($row['fullname']); ?></h3>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Select New Status</label>
            <select name="status" class="form-select" required>
                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if($row['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="completed" <?php if($row['status']=='completed') echo 'selected'; ?>>Completed</option>
                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>
        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
        <a href="Viewappointments.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
