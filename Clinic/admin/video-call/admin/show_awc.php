<?php
// ================ DATABASE CONNECTION ================
$host = "localhost";
$user = "root";
$password = "";
$database = "clinic";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ================ UPDATE STATUS ================
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $doctor_notes = mysqli_real_escape_string($conn, $_POST['doctor_notes']);
    
    $update_query = "UPDATE appointmentcall SET status='$new_status', doctor_notes='$doctor_notes' WHERE id=$id";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>
            alert('✅ Appointment updated successfully!');
            window.location.href = window.location.href;
        </script>";
        exit();
    } else {
        echo "<script>alert('❌ Error updating appointment');</script>";
    }
}

// ================ DELETE APPOINTMENT ================
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM appointmentcall WHERE id=$delete_id";
    
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>
            alert('🗑️ Appointment deleted!');
            window.location.href = window.location.pathname;
        </script>";
        exit();
    } else {
        echo "<script>alert('❌ Error deleting appointment');</script>";
    }
}

// ================ FETCH STATISTICS ================
$total_query = "SELECT COUNT(*) as total FROM appointmentcall";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_appointments = $total_row['total'] ?? 0;

$status_counts = [];
$status_query = "SELECT status, COUNT(*) as count FROM appointmentcall GROUP BY status";
$status_result = mysqli_query($conn, $status_query);
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_counts[$row['status']] = $row['count'];
}

$pending_appointments = $status_counts['Pending'] ?? 0;
$accepted_appointments = $status_counts['Accepted'] ?? 0;
$completed_appointments = $status_counts['Completed'] ?? 0;
$cancelled_appointments = $status_counts['Cancelled'] ?? 0;

// TODAY'S DATE
$today = date("Y-m-d");

// TODAY'S APPOINTMENTS COUNT - Make sure to get ALL appointments for today
$today_query = "SELECT COUNT(*) as today FROM appointmentcall WHERE DATE(appointment_date) = '$today'";
$today_result = mysqli_query($conn, $today_query);

// Check if query failed
if (!$today_result) {
    die("Error in today count query: " . mysqli_error($conn));
}

$today_row = mysqli_fetch_assoc($today_result);
$today_appointments = $today_row['today'] ?? 0;

// Debug: Uncomment to check if date format is correct
// echo "<!-- Today's date: $today, Appointments found: $today_appointments -->";

// ================ FETCH TODAY'S APPOINTMENTS ================
// Use DATE() function to ensure proper date comparison regardless of time
$today_appointments_query = "SELECT * FROM appointmentcall 
                             WHERE DATE(appointment_date) = '$today' 
                             ORDER BY appointment_time ASC";

$today_appointments_result = mysqli_query($conn, $today_appointments_query);

// Check if query failed
if (!$today_appointments_result) {
    die("Error in today appointments query: " . mysqli_error($conn));
}

// Debug: Get count of today's appointments
$today_actual_count = mysqli_num_rows($today_appointments_result);
// echo "<!-- Today's appointments actual count: $today_actual_count -->";

// ================ FETCH ALL APPOINTMENTS ================
$appointments_query = "SELECT * FROM appointmentcall ORDER BY appointment_date DESC, appointment_time DESC";
$appointments_result = mysqli_query($conn, $appointments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: #f5f7fa;
            padding: 15px;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px 12px 0 0;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .card.total { border-left-color: #667eea; }
        .card.pending { border-left-color: #ff9f43; }
        .card.accepted { border-left-color: #0abde3; }
        .card.completed { border-left-color: #10ac84; }
        .card.cancelled { border-left-color: #dc3545; }
        .card.today { border-left-color: #9b59b6; }

        .card-title {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3436;
        }

        .content-section {
            padding: 20px;
        }

        .section-title {
            font-size: 1.4rem;
            color: #2d3436;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .today-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .today-section .section-title {
            color: #92400e;
            border-bottom-color: #fbbf24;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .today-table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #fbbf24;
            background: white;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .appointments-table th {
            padding: 12px 10px;
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }

        .today-table th {
            background: #fef3c7;
            color: #92400e;
            border-bottom-color: #fbbf24;
        }

        .appointments-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f3f4;
            color: #5f6368;
            vertical-align: middle;
        }

        .appointments-table tbody tr:hover {
            background: #f8f9fa;
        }

        .today-table tbody tr:hover {
            background: #fffbeb;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-update {
            background: #17a2b8;
            color: white;
        }

        .btn-update:hover {
            background: #138496;
            transform: scale(1.05);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* ===== SMALL MODAL STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            width: 95%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .modal-header h3 {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 0.9rem;
            background: #f8f9fa;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
        }

        .form-textarea {
            min-height: 80px;
            resize: vertical;
            font-family: inherit;
        }

        .modal-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 10px 10px;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
        }

        .btn-primary {
            background: #28a745;
            color: white;
            padding: 8px 15px;
        }

        .footer {
            text-align: center;
            padding: 15px;
            color: #6c757d;
            font-size: 0.85rem;
            border-top: 1px solid #e9ecef;
        }

        .patient-name {
            font-weight: 600;
            color: #2d3436;
        }

        .date-time {
            font-size: 0.85rem;
        }

        .notes-preview {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help;
        }

        .time-badge {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-block;
        }

        .refresh-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="fas fa-calendar-check"></i> Appointment Dashboard</h1>
                    <p>Manage and update appointments quickly</p>
                </div>
                <button class="refresh-btn" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="card total">
                <div class="card-title">Total Appointments</div>
                <div class="card-value"><?php echo $total_appointments; ?></div>
            </div>
            
            <div class="card pending">
                <div class="card-title">Pending</div>
                <div class="card-value"><?php echo $pending_appointments; ?></div>
            </div>
            
            <div class="card accepted">
                <div class="card-title">Accepted</div>
                <div class="card-value"><?php echo $accepted_appointments; ?></div>
            </div>
            
            <div class="card completed">
                <div class="card-title">Completed</div>
                <div class="card-value"><?php echo $completed_appointments; ?></div>
            </div>
            
            <div class="card cancelled">
                <div class="card-title">Cancelled</div>
                <div class="card-value"><?php echo $cancelled_appointments; ?></div>
            </div>
            
            <div class="card today">
                <div class="card-title">Today's Appointments</div>
                <div class="card-value"><?php echo $today_appointments; ?></div>
            </div>
        </div>

        <!-- TODAY'S APPOINTMENTS SECTION -->
        <div class="content-section">
            <div class="today-section">
                <h2 class="section-title">
                    <i class="fas fa-sun"></i> Today's Appointments - <?php echo date("F j, Y (l)"); ?>
                </h2>
                
                <?php 
                // Reset the result pointer to get fresh data
                mysqli_data_seek($today_appointments_result, 0);
                $today_count = mysqli_num_rows($today_appointments_result);
                
                if($today_count > 0): 
                ?>
                    <div class="today-table-wrapper">
                        <table class="appointments-table today-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient Name</th>
                                    <th>Problem</th>
                                    <th>Status</th>
                                    <th>Doctor Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row = mysqli_fetch_assoc($today_appointments_result)): 
                                    $id = $row['id'];
                                    $patient_name = addslashes($row['patient_name']);
                                    $status = addslashes($row['status']);
                                    $notes = isset($row['doctor_notes']) ? addslashes($row['doctor_notes']) : '';
                                ?>
                                    <tr style="border-left: 3px solid <?php 
                                        echo $row['status'] == 'Pending' ? '#ff9f43' : 
                                             ($row['status'] == 'Accepted' ? '#0abde3' : 
                                             ($row['status'] == 'Completed' ? '#10ac84' : '#dc3545')); 
                                    ?>;">
                                        <td>
                                            <span class="time-badge">
                                                <i class="fas fa-clock"></i> <?php echo date("h:i A", strtotime($row['appointment_time'])); ?>
                                            </span>
                                        </td>
                                        <td class="patient-name">
                                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($row['patient_name']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['problem']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(!empty($row['doctor_notes'])): ?>
                                                <span class="notes-preview" title="<?php echo htmlspecialchars($row['doctor_notes']); ?>">
                                                    <?php echo htmlspecialchars(substr($row['doctor_notes'], 0, 30)); ?>...
                                                </span>
                                            <?php else: ?>
                                                <span style="color: #adb5bd; font-style: italic;">No notes</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-update" onclick="openUpdateModal(
                                                    <?php echo $id; ?>,
                                                    '<?php echo $patient_name; ?>',
                                                    '<?php echo $status; ?>',
                                                    '<?php echo $notes; ?>'
                                                )">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                                <button class="btn btn-delete" onclick="confirmDelete(
                                                    <?php echo $id; ?>,
                                                    '<?php echo $patient_name; ?>'
                                                )">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-day"></i>
                        <p>No appointments scheduled for today</p>
                        <p style="font-size: 0.85rem; margin-top: 10px;">Enjoy your day! 🎉</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Appointments Section -->
        <div class="content-section">
            <h2 class="section-title"><i class="fas fa-list"></i> All Appointments</h2>
            
            <?php if(mysqli_num_rows($appointments_result) > 0): ?>
                <div class="table-wrapper">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Problem</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while($row = mysqli_fetch_assoc($appointments_result)): 
                                $id = $row['id'];
                                $patient_name = addslashes($row['patient_name']);
                                $status = addslashes($row['status']);
                                $notes = isset($row['doctor_notes']) ? addslashes($row['doctor_notes']) : '';
                                
                                // Highlight today's appointments in the all appointments list
                                $is_today = ($row['appointment_date'] == $today);
                                $row_style = $is_today ? 'style="background-color: #fffbf0;"' : '';
                            ?>
                                <tr <?php echo $row_style; ?>>
                                    <td><strong>#<?php echo $id; ?></strong></td>
                                    <td class="patient-name"><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                    <td class="date-time">
                                        <div><?php echo date("M j, Y", strtotime($row['appointment_date'])); ?></div>
                                        <div><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></div>
                                        <?php if($is_today): ?>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Today</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['problem']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['doctor_notes'])): ?>
                                            <span class="notes-preview" title="<?php echo htmlspecialchars($row['doctor_notes']); ?>">
                                                <?php echo htmlspecialchars(substr($row['doctor_notes'], 0, 30)); ?>...
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #adb5bd; font-style: italic;">No notes</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-update" onclick="openUpdateModal(
                                                <?php echo $id; ?>,
                                                '<?php echo $patient_name; ?>',
                                                '<?php echo $status; ?>',
                                                '<?php echo $notes; ?>'
                                            )">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <button class="btn btn-delete" onclick="confirmDelete(
                                                <?php echo $id; ?>,
                                                '<?php echo $patient_name; ?>'
                                            )">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-calendar-times fa-2x" style="margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>No appointments found.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Clinic System • <?php echo date("M j, Y"); ?> • Auto-refresh on update</p>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal-overlay" id="updateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Update Appointment</h3>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="id" id="updateId">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Patient</label>
                        <input type="text" id="updatePatientName" class="form-input" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Current Status</label>
                        <input type="text" id="updateCurrentStatus" class="form-input" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Status</label>
                        <select name="status" id="updateStatus" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Doctor Notes</label>
                        <textarea name="doctor_notes" id="updateDoctorNotes" class="form-textarea" 
                                  placeholder="Enter notes..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff6b6b 0%, #c23616 100%);">
                <h3><i class="fas fa-exclamation-triangle"></i> Delete Appointment</h3>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px;">
                    Delete appointment for <strong id="deletePatientName"></strong>?
                </p>
                <p style="color: #dc3545; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <a href="" id="deleteConfirmLink" class="btn btn-delete">Delete</a>
            </div>
        </div>
    </div>

    <script>
        // Update Modal Functions
        function openUpdateModal(id, patientName, currentStatus, doctorNotes) {
            document.getElementById('updateModal').style.display = 'flex';
            document.getElementById('updateId').value = id;
            document.getElementById('updatePatientName').value = patientName;
            document.getElementById('updateCurrentStatus').value = currentStatus;
            document.getElementById('updateStatus').value = currentStatus;
            document.getElementById('updateDoctorNotes').value = doctorNotes;
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        // Delete Modal Functions
        function confirmDelete(id, patientName) {
            document.getElementById('deleteModal').style.display = 'flex';
            document.getElementById('deletePatientName').textContent = patientName;
            document.getElementById('deleteConfirmLink').href = '?delete_id=' + id;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeUpdateModal();
                closeDeleteModal();
            }
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            const updateModal = document.getElementById('updateModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === updateModal) closeUpdateModal();
            if (event.target === deleteModal) closeDeleteModal();
        }
        
        // Auto refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>