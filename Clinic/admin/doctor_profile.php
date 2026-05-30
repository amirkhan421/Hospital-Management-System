<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Doctorlogin.php");
    exit();
}

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "Clinic");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query for doctor data - FIXED: Use correct column names
$query = "SELECT * FROM `register_doctor`";
$doctorResult = mysqli_query($conn, $query);

if (!$doctorResult) {
    die("Query Failed: " . mysqli_error($conn));
}

// Fetch all doctors into an array for stats calculation
$doctors = [];
while ($row = mysqli_fetch_assoc($doctorResult)) {
    $doctors[] = $row;
}

// Reset the result pointer for table display
mysqli_data_seek($doctorResult, 0);

// Calculate statistics
$totalDoctors = count($doctors);
$specializations = [];
foreach ($doctors as $doctor) {
    // FIXED: Use correct column name 'specialization' instead of 'specialiation'
    if (!empty($doctor['specialization'])) {
        $specializations[] = $doctor['specialization'];
    }
}
$uniqueSpecializations = array_unique($specializations);
$totalSpecializations = count($uniqueSpecializations);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor Profile Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  
  <!-- Animate.css for additional animations -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      font-family: "Poppins", sans-serif;
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: white;
      overflow-x: hidden;
    }

    /* Animated Background Elements */
    .bg-elements {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      pointer-events: none;
    }

    .bg-circle {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
      animation: float 20s infinite linear;
    }

    .circle-1 {
      width: 300px;
      height: 300px;
      top: 10%;
      right: 5%;
      animation-delay: 0s;
    }

    .circle-2 {
      width: 200px;
      height: 200px;
      bottom: 15%;
      left: 5%;
      animation-delay: 5s;
      background: rgba(255, 221, 89, 0.1);
    }

    .circle-3 {
      width: 150px;
      height: 150px;
      top: 40%;
      left: 15%;
      animation-delay: 10s;
      background: rgba(255, 111, 97, 0.1);
    }

    /* Container */
    .table-container {
      background: rgba(255, 255, 255, 0.08);
      padding: 50px 30px;
      border-radius: 30px;
      backdrop-filter: blur(25px);
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.6),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 0 30px rgba(255, 255, 255, 0.05);
      max-width: 1300px;
      width: 100%;
      margin-bottom: 50px;
      animation: fadeInDown 1.2s ease-out;
      border: 1px solid rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
    }

    .table-container::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 255, 255, 0.03) 50%,
        transparent 70%
      );
      animation: shine 8s infinite linear;
      pointer-events: none;
    }

    /* Header */
    .header-section {
      text-align: center;
      margin-bottom: 50px;
      position: relative;
    }

    h2 {
      font-weight: 800;
      font-size: 3rem;
      margin-bottom: 15px;
      background: linear-gradient(45deg, #ffdd59, #ff6f61, #1c92d2);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      animation: titleGlow 3s infinite alternate;
    }

    .subtitle {
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.1rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      animation: fadeIn 2s ease;
    }

    h2 i {
      margin-right: 15px;
      animation: iconBounce 2s infinite, rotate 10s infinite linear;
    }

    /* Stats Cards */
    .stats-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 40px;
      flex-wrap: wrap;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 25px;
      min-width: 200px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      animation: cardFloat 6s infinite ease-in-out;
    }

    .stat-card:nth-child(2) {
      animation-delay: 2s;
    }

    .stat-card:nth-child(3) {
      animation-delay: 4s;
    }

    .stat-card:hover {
      transform: translateY(-10px) scale(1.05);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(45deg, #ffdd59, #ff6f61);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .stat-label {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.8);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-top: 10px;
    }

    /* Table Styling */
    .table-wrapper {
      border-radius: 20px;
      overflow-x: auto;
      position: relative;
      animation: fadeInUp 1.5s ease-out 0.5s both;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: linear-gradient(135deg, rgba(28, 146, 210, 0.1), rgba(242, 252, 254, 0.05));
      color: #fff;
      animation: tableEntrance 1s ease-out;
    }

    th, td {
      text-align: center;
      padding: 22px 18px;
      font-weight: 500;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    th {
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      font-weight: 700;
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      position: sticky;
      top: 0;
      z-index: 10;
      animation: headerPulse 4s infinite;
    }

    th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, transparent, #fff, transparent);
      animation: headerLine 3s infinite;
    }

    td {
      color: #e0e0e0;
      background: rgba(0, 0, 0, 0.3);
    }

    tr:nth-child(even) td {
      background: rgba(0, 0, 0, 0.4);
    }

    tr {
      animation: rowEntrance 0.6s ease-out forwards;
      opacity: 0;
      transform: translateX(-30px);
    }

    tr:nth-child(1) { animation-delay: 0.1s; }
    tr:nth-child(2) { animation-delay: 0.2s; }
    tr:nth-child(3) { animation-delay: 0.3s; }
    tr:nth-child(4) { animation-delay: 0.4s; }
    tr:nth-child(5) { animation-delay: 0.5s; }
    tr:nth-child(6) { animation-delay: 0.6s; }
    tr:nth-child(7) { animation-delay: 0.7s; }
    tr:nth-child(8) { animation-delay: 0.8s; }
    tr:nth-child(9) { animation-delay: 0.9s; }
    tr:nth-child(10) { animation-delay: 1.0s; }

    tr:hover td {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.02);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      color: white;
      z-index: 2;
    }

    /* Specialization Badge */
    .specialization-badge {
      display: inline-block;
      padding: 6px 15px;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      color: white;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      animation: badgePulse 2s infinite;
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .btn-animated {
      padding: 12px 30px;
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      animation: buttonPulse 3s infinite;
    }

    .btn-animated:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 65, 108, 0.4);
      animation: none;
    }

    .btn-animated::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 255, 255, 0.3) 50%,
        transparent 70%
      );
      transform: rotate(45deg);
      transition: all 0.5s ease;
    }

    .btn-animated:hover::after {
      left: 100%;
    }

    /* No Data Message */
    .no-data {
      text-align: center;
      padding: 60px;
      color: rgba(255, 255, 255, 0.6);
      font-size: 1.2rem;
    }

    .no-data i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    /* Animations */
    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      33% { transform: translateY(-20px) rotate(120deg); }
      66% { transform: translateY(20px) rotate(240deg); }
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-80px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(80px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes iconBounce {
      0%, 100% {
        transform: translateY(0) rotate(0deg);
      }
      50% {
        transform: translateY(-15px) rotate(10deg);
      }
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes titleGlow {
      0% { text-shadow: 0 4px 20px rgba(255, 221, 89, 0.5); }
      100% { text-shadow: 0 4px 30px rgba(255, 111, 97, 0.7); }
    }

    @keyframes shine {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes cardFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    @keyframes headerPulse {
      0%, 100% { background: linear-gradient(135deg, #ff416c, #ff4b2b); }
      50% { background: linear-gradient(135deg, #ff4b2b, #ff416c); }
    }

    @keyframes headerLine {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    @keyframes rowEntrance {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes tableEntrance {
      0% {
        transform: scale(0.8);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    @keyframes badgePulse {
      0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3); }
      50% { transform: scale(1.05); box-shadow: 0 4px 20px rgba(79, 172, 254, 0.5); }
    }

    @keyframes buttonPulse {
      0%, 100% { box-shadow: 0 5px 15px rgba(255, 65, 108, 0.3); }
      50% { box-shadow: 0 5px 25px rgba(255, 65, 108, 0.5); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(45deg, #ff416c, #ff4b2b);
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(45deg, #ff4b2b, #ff416c);
    }

    /* Responsive */
    @media(max-width: 992px) {
      .table-container {
        padding: 30px 20px;
      }

      h2 {
        font-size: 2.2rem;
      }

      th, td {
        padding: 15px 10px;
      }

      .stats-container {
        gap: 20px;
      }

      .stat-card {
        min-width: 150px;
        padding: 20px;
      }
    }

    @media(max-width: 768px) {
      h2 {
        font-size: 1.8rem;
      }

      .table-container {
        padding: 20px 15px;
      }

      th, td {
        padding: 12px 8px;
        font-size: 0.85rem;
      }

      .specialization-badge {
        padding: 4px 10px;
        font-size: 0.75rem;
      }
    }

    @media(max-width: 576px) {
      body {
        padding: 20px 10px;
      }

      .stats-container {
        flex-direction: column;
        align-items: center;
      }

      .stat-card {
        width: 100%;
        max-width: 250px;
      }

      .action-buttons {
        flex-direction: column;
        align-items: center;
      }

      .btn-animated {
        width: 100%;
        max-width: 250px;
      }
      
      th, td {
        padding: 10px 6px;
        font-size: 0.75rem;
      }
    }
  </style>
</head>

<body>

  <!-- Animated Background Elements -->
  <div class="bg-elements">
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
  </div>

  <div class="table-container animate__animated animate__fadeIn">

    <div class="header-section">
      <h2><i class="fa-solid fa-user-doctor"></i> Doctor Profile Details</h2>
      <div class="subtitle">Advanced Management Dashboard</div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number"><?php echo $totalDoctors; ?></div>
        <div class="stat-label">Total Doctors</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $totalSpecializations; ?></div>
        <div class="stat-label">Specializations</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">24/7</div>
        <div class="stat-label">Active System</div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
      <?php if ($totalDoctors > 0): ?>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Specialization</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($doctorResult)): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['fullname'] ?? $row['Full_Name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                <td>
                  <span class="specialization-badge">
                    <?php echo htmlspecialchars($row['specialization'] ?? $row['Specialization'] ?? 'N/A'); ?>
                  </span>
                </td>
                <td>
                  <span style="color: #4cd964; font-weight: 600;">
                    <i class="fa-solid fa-circle" style="font-size: 0.7rem;"></i> Active
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="no-data">
          <i class="fa-solid fa-user-slash"></i>
          <p>No doctors found in the database.</p>
          <p style="font-size: 0.9rem; margin-top: 10px;">Please add some doctors to display here.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <button class="btn-animated" onclick="location.href='Doctorlogin.php'">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
      </button>
      <button class="btn-animated" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print Report
      </button>
      <button class="btn-animated" onclick="exportToExcel()">
        <i class="fa-solid fa-file-export"></i> Export Data
      </button>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Add more interactivity
    document.addEventListener('DOMContentLoaded', function() {
      // Add hover effect to table rows
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
          this.style.transform = 'scale(1.02)';
          this.style.zIndex = '2';
        });
        
        row.addEventListener('mouseleave', function() {
          this.style.transform = 'scale(1)';
          this.style.zIndex = '1';
        });
      });

      // Add click effect to buttons
      const buttons = document.querySelectorAll('.btn-animated');
      buttons.forEach(button => {
        button.addEventListener('click', function() {
          this.style.transform = 'scale(0.95)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 150);
        });
      });

      // Add typing effect to subtitle
      const subtitle = document.querySelector('.subtitle');
      if (subtitle) {
        const text = subtitle.textContent;
        subtitle.textContent = '';
        let i = 0;
        function typeWriter() {
          if (i < text.length) {
            subtitle.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
          }
        }
        setTimeout(typeWriter, 1000);
      }
    });

    // Export to Excel function
    function exportToExcel() {
      const table = document.querySelector('table');
      if (table) {
        const html = table.outerHTML;
        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'doctors_report.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        // Show success message
        alert('Report exported successfully!');
      } else {
        alert('No data to export!');
      }
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      // Ctrl+P for print
      if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
      }
      // Ctrl+E for export
      if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportToExcel();
      }
    });
  </script>
</body>

</html>
