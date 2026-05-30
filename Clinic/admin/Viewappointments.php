<?php
session_start();
$con = mysqli_connect('localhost', 'root', '', 'clinic');
if (!$con) { die('Database Connection Failed: ' . mysqli_connect_error()); }

$appointments_result = mysqli_query($con, "SELECT * FROM `appointments` ORDER BY appointment_date DESC");
$total_appointments = mysqli_num_rows($appointments_result);
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin - Manage Appointments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    overflow-x: hidden;
    animation: bgMove 30s ease-in-out infinite alternate;
    color: #333;
}

@keyframes bgMove {
0% { background-position: 0% 50%; }
50% { background-position: 100% 50%; }
100% { background-position: 0% 50%; }
}

.navbar { box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
.admin-card {
background: rgba(255,255,255,0.95);
border-radius: 15px;
padding: 2rem;
box-shadow: 0 10px 25px rgba(0,0,0,0.1);
animation: fadeInUp 1s ease forwards;
}
@keyframes fadeInUp {
0% { opacity: 0; transform: translateY(50px);}
100% { opacity: 1; transform: translateY(0);}
}

.table th {
background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
color: white;
border: none;
}

.status-badge {
padding: 6px 12px;
border-radius: 20px;
font-size: 12px;
font-weight: 600;
transition: all 0.3s ease;
}
.status-badge:hover { transform: scale(1.05); }

.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d1eddc; color: #0f5132; }
.status-completed { background: #d1ecf1; color: #0c5460; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.btn-action {
padding: 5px 10px;
font-size: 12px;
margin: 2px;
transition: all 0.3s ease;
}
.btn-action:hover { transform: scale(1.05); }

.admin-header {
background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
color: white;
padding: 30px 0;
margin-bottom: 30px;
text-shadow: 0 0 10px rgba(0,0,0,0.3);
animation: fadeInDown 1s ease forwards;
}
@keyframes fadeInDown {
0% { opacity: 0; transform: translateY(-30px);}
100% { opacity: 1; transform: translateY(0);}
}

/* Table row hover animation */
.table-hover tbody tr {
transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.table-hover tbody tr:hover {
transform: translateY(-5px);
box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Floating total badge */
.badge {
animation: floatBadge 3s ease-in-out infinite alternate;
}
@keyframes floatBadge {
0%,100% { transform: translateY(0px); }
50% { transform: translateY(-5px); }
} </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
<div class="container">
<a class="navbar-brand fw-bold" href="adminlogin.php"><i class="fa-solid fa-hospital me-2"></i>Admin Panel</a>
<div class="navbar-nav ms-auto">
<a class="nav-link text-white" href="adminlogin.php"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a>
<a class="nav-link text-warning" href="LogoutAdmin.php"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</a>
</div>
</div>
</nav>

<div class="admin-header text-center">
<h1 class="fw-bold"><i class="fa-solid fa-calendar-check me-3"></i>Manage Appointments</h1>
<p class="mb-0">View all patient appointments</p>
</div>

<div class="container">
<div class="admin-card">
<div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-list-check me-2"></i>All Appointments</h4>
<span class="badge bg-primary fs-6">Total: <?php echo $total_appointments; ?></span>
</div>

<?php if($total_appointments > 0): ?>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
<th>#</th>
<th>Patient</th>
<th>Email</th>
<th>Phone</th>
<th>Date</th>
<th>Time</th>
<th>Doctor</th>
<th>Message</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php 
$counter=1; 
while($row = mysqli_fetch_assoc($appointments_result)): 
    $status = isset($row['status']) ? $row['status'] : 'pending';
    $status_class = 'status-pending';
    if($status == 'confirmed') $status_class = 'status-confirmed';
    if($status == 'completed') $status_class = 'status-completed';
    if($status == 'cancelled') $status_class = 'status-cancelled';
?>
<tr>
<td><?php echo $counter++; ?></td>
<td><?php echo htmlspecialchars($row['fullname']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['phone']); ?></td>
<td><?php echo date('M j, Y', strtotime($row['appointment_date'])); ?></td>
<td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
<td><?php echo htmlspecialchars($row['doctor']); ?></td>
<td><?php echo htmlspecialchars($row['message']); ?></td>
<td>
<span class="status-badge <?php echo $status_class; ?>">
<?php echo ucfirst($status); ?>
</span>
</td>
<td>
<a href="update_status.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm btn-action">
<i class="fa-solid fa-pen-to-square"></i> Update
</a>
<a href="delete_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Are you sure you want to delete this appointment?')">
<i class="fa-solid fa-trash"></i> Delete
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="text-center py-5">
<i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
<h5 class="text-muted">No appointments found</h5>
<p class="text-muted">There are no appointments yet.</p>
</div>
<?php endif; ?>
</div>
</div>

<footer class="footer mt-5 py-4 bg-light text-center text-muted">
<div class="container"><small>© 2025 Clinic Management System - Admin Panel</small></div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
