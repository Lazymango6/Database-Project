<?php
header("Content-Type: application/json");
include '../db.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid or no JSON data received"]);
    exit;
}

if (!isset($data->user_id, $data->name)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$user_id = $conn->real_escape_string($data->user_id);
$name = $conn->real_escape_string($data->name);
$position = $conn->real_escape_string($data->position ?? '');
$salary = floatval($data->salary ?? 0);
$hourly_rate = floatval($data->hourly_rate ?? 0);
$tax_rate = floatval($data->tax_rate ?? 0);

// Check if the user is valid and unassigned to any employee
$stmt = $conn->prepare("SELECT User_ID FROM employee_user WHERE User_ID = ? AND Employee_ID IS NULL");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user or user already assigned to an employee"]);
    exit;
}

$conn->begin_transaction();

try {
    // Step 1: Insert into employee_info
    $stmt1 = $conn->prepare("INSERT INTO employee_info (Name, Position, Salary, Hourly_Rate, Tax_Rate) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("ssddd", $name, $position, $salary, $hourly_rate, $tax_rate);
    $stmt1->execute();
    $employee_id = $conn->insert_id;

    // Step 2: Update employee_user to link user to the new employee
    $stmt2 = $conn->prepare("UPDATE employee_user SET Employee_ID = ? WHERE User_ID = ?");
    $stmt2->bind_param("ii", $employee_id, $user_id);
    $stmt2->execute();

    $conn->commit();

    echo json_encode(["status" => "success", "employee_id" => $employee_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>


