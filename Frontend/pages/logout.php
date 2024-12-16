<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: http://localhost/OG_photo_2/public/login.php"); // Redirect to login page
exit();
?>