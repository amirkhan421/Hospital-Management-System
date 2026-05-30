<?php
session_start();
session_unset();
session_destroy();
header("Location: doctor_login_call.php");
exit();
?>
