<?php
header("Content-Type: application/json");
include '../db.php';

// Fetch all users (exclude admins if needed)
$sql = "SELECT User_ID, Username FROM employee_user WHERE Role = 'Employee'";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
?>
