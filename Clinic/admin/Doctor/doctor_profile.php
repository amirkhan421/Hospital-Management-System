<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Doctorlogin.php");
    exit();
}

// Database Connection - YEH EK LINE CHANGE KARDI (hospital -> clinic)
$conn = mysqli_connect("localhost", "root", "", "Clinic");

// Query for doctor data
$query = "SELECT * FROM `register_doctor`";
$doctorResult = mysqli_query($conn, $query);
if (!$doctorResult) {
    die("Query Failed: " . mysqli_error($conn));
}
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

    .circle-1 { width: 300px; height: 300px; top: 10%; right: 5%; animation-delay: 0s; }
    .circle-2 { width: 200px; height: 200px; bottom: 15%; left: 5%; animation-delay: 5s; background: rgba(255, 221, 89, 0.1); }
    .circle-3 { width: 150px; height: 150px; top: 40%; left: 15%; animation-delay: 10s; background: rgba(255, 111, 97, 0.1); }

    .table-container {
      background: rgba(255, 255, 255, 0.08);
      padding: 50px 30px;
      border-radius: 30px;
      backdrop-filter: blur(25px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
      max-width: 1300px;
      width: 100%;
      margin-bottom: 50px;
      animation: fadeInDown 1.2s ease-out;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .header-section {
      text-align: center;
      margin-bottom: 50px;
    }

    h2 {
      font-weight: 800;
      font-size: 3rem;
      margin-bottom: 15px;
      background: linear-gradient(45deg, #ffdd59, #ff6f61, #1c92d2);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .subtitle {
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.1rem;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

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
      transition: all 0.4s ease;
    }

    .stat-card:hover {
      transform: translateY(-10px) scale(1.05);
      background: rgba(255, 255, 255, 0.2);
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
      margin-top: 10px;
    }

    .table-wrapper {
      border-radius: 20px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(0, 0, 0, 0.3);
      color: #fff;
    }

    th, td {
      text-align: center;
      padding: 18px 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    th {
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    td {
      background: rgba(0, 0, 0, 0.2);
    }

    tr:hover td {
      background: rgba(255, 255, 255, 0.15);
    }

    .specialization-badge {
      display: inline-block;
      padding: 6px 15px;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      color: white;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
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
    }

    .btn-animated:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 65, 108, 0.4);
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      33% { transform: translateY(-20px) rotate(120deg); }
      66% { transform: translateY(20px) rotate(240deg); }
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-80px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
      .table-container { padding: 30px 20px; }
      h2 { font-size: 2rem; }
      th, td { padding: 12px 8px; font-size: 0.85rem; }
      .stat-card { min-width: 150px; padding: 20px; }
      .stat-number { font-size: 2rem; }
    }
  </style>
</head>

<body>

  <div class="bg-elements">
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
  </div>

  <div class="table-container">

    <div class="header-section">
      <h2><i class="fa-solid fa-user-doctor"></i> Doctor Profile Details</h2>
      <div class="subtitle">Advanced Management Dashboard</div>
    </div>

    <!-- Stats Cards -->
    <?php
    mysqli_data_seek($doctorResult, 0);
    $totalDoctors = mysqli_num_rows($doctorResult);
    $specializations = [];
    while ($row = mysqli_fetch_assoc($doctorResult)) {
        $specializations[] = $row['Specialization'];
    }
    $uniqueSpecializations = array_unique($specializations);
    mysqli_data_seek($doctorResult, 0);
    ?>
    
    <div class="stats-container">
      <div class="stat-card">
        <div class="stat-number"><?= $totalDoctors ?></div>
        <div class="stat-label">Total Doctors</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= count($uniqueSpecializations) ?></div>
        <div class="stat-label">Specializations</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">24/7</div>
        <div class="stat-label">Active System</div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Specialization</th>
            <th>License Number</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($doctorResult)) { ?>
          <tr>
            <td><?= $row['ID']; ?></td>
            <td>Dr. <?= $row['Full_Name']; ?></td>
            <td><?= $row['Username']; ?></td>
            <td><?= $row['Email']; ?></td>
            <td><?= $row['Phone']; ?></td>
            <td><span class="specialization-badge"><?= $row['Specialization']; ?></span></td>
            <td><?= $row['License_Number']; ?></td>
            <td>
              <span style="color: #4cd964; font-weight: 600;">
                <i class="fa-solid fa-circle" style="font-size: 0.7rem;"></i> Active
              </span>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <button class="btn-animated" onclick="location.href='indexdoc.php'">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    function exportToExcel() {
      const table = document.querySelector('table');
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
      alert('Report exported successfully!');
    }
  </script>
</body>

</html>