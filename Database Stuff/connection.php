<?php
$servername = "localhost";
$username = "root"; // Change this if needed
$password = ""; // Default is empty for XAMPP/MAMP
$database = "paycalc_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
