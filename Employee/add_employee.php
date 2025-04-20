<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include '../db.php';

$data = json_decode(file_get_contents("php://input"));

// Debug: Check if we even got any data
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid or no JSON data received"]);
    exit;
}

if (isset($data->name)) {
    $name = $conn->real_escape_string($data->name);
    $department = $conn->real_escape_string($data->department ?? '');
    $position = $conn->real_escape_string($data->position ?? '');
    $salary = floatval($data->salary ?? 0);
    $hourly_rate = floatval($data->hourly_rate ?? 0);
    $tax_rate = floatval($data->tax_rate ?? 0);

    $sql = "INSERT INTO employee_info (Name, Department, Position, Salary, Hourly_Rate, Tax_Rate) 
            VALUES ('$name', '$department', '$position', $salary, $hourly_rate, $tax_rate)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing name field"]);
}
?>


