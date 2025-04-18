<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include '../db.php';
session_start();

// Parse input
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password)) {
    echo json_encode(["status" => "error", "message" => "Missing input"]);
    exit();
}

$username = $data->username;
$password = $data->password;

// Log input for debugging (remove in production!)
error_log("Login attempt: $username / $password");

// Query database
$sql = "SELECT * FROM employee_user WHERE Username = '$username' AND Password = '$password'";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    exit();
}

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $user['Role'];

    echo json_encode([
        "status" => "success",
        "role" => $user['Role']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}
?>



