<?php
session_start();

/* 🔹 Database Connection */
$conn = mysqli_connect("localhost", "root", "", "clinic");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<div style='background:#f8f9fa; padding:15px; border-radius:5px; margin:20px;'>";
echo "<h3 style='color:#dc3545;'>DEBUG MODE - Doctor Login</h3>";

/* 🔹 Login Logic */
$error = "";

if (isset($_POST['login'])) {

    // Input clean
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    echo "<p><strong>DEBUG:</strong> Username entered: <code>'$username'</code></p>";
    echo "<p><strong>DEBUG:</strong> Password entered: <code>'$password'</code></p>";

    // 🔹 **DEBUG: Check if table exists**
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'doctors_register_call'");
    if (mysqli_num_rows($check_table) == 0) {
        die("<p style='color:red;'><strong>ERROR:</strong> Table 'doctors_register_call' does not exist!</p>");
    } else {
        echo "<p><strong>DEBUG:</strong> Table 'doctors_register_call' exists ✅</p>";
    }

    // 🔹 **DEBUG: Show all doctors**
    $all_doctors = mysqli_query($conn, "SELECT id, username, password, full_name FROM doctors_register_call");
    echo "<p><strong>DEBUG:</strong> Doctors in database:</p>";
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr style='background:#e9ecef;'><th>ID</th><th>Username</th><th>Password (from DB)</th><th>Full Name</th></tr>";
    while($doc = mysqli_fetch_assoc($all_doctors)) {
        echo "<tr>";
        echo "<td>" . $doc['id'] . "</td>";
        echo "<td>" . $doc['username'] . "</td>";
        echo "<td>" . $doc['password'] . "</td>";
        echo "<td>" . $doc['full_name'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 🔹 **Query for specific user**
    $query = "SELECT id, username, full_name, specialization, password 
              FROM doctors_register_call 
              WHERE username='$username'";

    echo "<p><strong>DEBUG:</strong> Running query: <code>$query</code></p>";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        $num_rows = mysqli_num_rows($result);
        echo "<p><strong>DEBUG:</strong> Found $num_rows user(s) with username '$username'</p>";
        
        if ($num_rows == 1) {
            $doctor = mysqli_fetch_assoc($result);
            
            echo "<p><strong>DEBUG:</strong> Database password for '$username': <code>'" . $doctor['password'] . "'</code></p>";
            echo "<p><strong>DEBUG:</strong> Entered password: <code>'$password'</code></p>";
            echo "<p><strong>DEBUG:</strong> Password comparison: " . 
                 ($doctor['password'] === $password ? 
                 "<span style='color:green;'>MATCHES ✅</span>" : 
                 "<span style='color:red;'>DOES NOT MATCH ❌</span>") . "</p>";
            
            if ($doctor['password'] === $password) {
                echo "<p style='color:green;'><strong>SUCCESS:</strong> Password matches! Redirecting...</p>";
                
                // ✅ **SESSION SET CORRECTLY**
                $_SESSION['doctor_id'] = $doctor['id'];
                $_SESSION['username'] = $doctor['username'];
                $_SESSION['doctor_username'] = $doctor['username'];
                $_SESSION['doctor_name'] = $doctor['full_name'];
                $_SESSION['doctor_specialization'] = $doctor['specialization'];
                $_SESSION['is_doctor_logged_in'] = true;
                $_SESSION['login_time'] = time();

                // ✅ **Set session cookie**
                setcookie('doctor_logged_in', 'true', time() + (30 * 24 * 60 * 60), "/");
                
                echo "<p><strong>DEBUG:</strong> Session variables set:</p>";
                echo "<ul>";
                foreach($_SESSION as $key => $value) {
                    echo "<li><strong>$key:</strong> $value</li>";
                }
                echo "</ul>";
                
                // Redirect
                header("Location: today_calls.php");
                exit();
                
            } else {
                $error = "Incorrect password!";
                echo "<p style='color:red;'><strong>ERROR:</strong> $error</p>";
                
                // Check for whitespace issues
                echo "<p><strong>DEBUG:</strong> Checking for whitespace issues...</p>";
                echo "<p>DB password length: " . strlen($doctor['password']) . " chars</p>";
                echo "<p>Entered password length: " . strlen($password) . " chars</p>";
                echo "<p>DB password (with htmlentities): '" . htmlentities($doctor['password']) . "'</p>";
            }
            
        } else {
            $error = "Username not found!";
            echo "<p style='color:red;'><strong>ERROR:</strong> $error</p>";
        }
    } else {
        $error = "Database query error: " . mysqli_error($conn);
        echo "<p style='color:red;'><strong>ERROR:</strong> $error</p>";
    }
}

echo "</div>";

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Login - DEBUG MODE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f8f9fa;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.login-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    max-width: 500px;
    margin: 0 auto;
}
.debug-info {
    background: #e9ecef;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
    font-size: 14px;
}
</style>
</head>
<body>

<div class="login-card">
    <h2 class="text-center mb-4">Doctor Login (DEBUG MODE)</h2>
    
    <?php if(isset($error) && !empty($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required 
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>
    
</div>

</body>
</html>