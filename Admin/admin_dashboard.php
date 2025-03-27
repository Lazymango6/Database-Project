<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php"); // Redirect to login if not a manager
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
</head>
<body>
    <h2>Welcome, Manager!</h2>
    <a href="logout.php">Logout</a>
    <!-- Dashboard Content Here -->
</body>
</html>