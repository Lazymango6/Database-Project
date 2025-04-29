<?php
session_start();
include '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Employee') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$username = $_SESSION['username'];

// Step 1: Get Employee_ID from username
$stmt = $conn->prepare("SELECT Employee_ID FROM employee_user WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
    exit();
}

$user = $result->fetch_assoc();
$employee_id = $user['Employee_ID'];

// Step 2: Get payroll + time data for the employee
$query = $conn->prepare("
    SELECT 
        ep.Pay_Period_Start,
        ep.Pay_Period_End,
        ep.Hourly_Rate,
        ep.BasePay,
        ep.Taxes,
        ep.Overtime_Pay,
        ep.Deductions,
        ep.Bonus,
        ep.NetPay,
        tt.Hours_Worked,
        tt.Overtime_Hours
    FROM employee_payroll ep
    JOIN time_tracking tt ON ep.Employee_ID = tt.Employee_ID 
        AND ep.Pay_Period_Start = tt.Pay_Period_Start 
        AND ep.Pay_Period_End = tt.Pay_Period_End
    WHERE ep.Employee_ID = ?
    ORDER BY ep.Pay_Period_End DESC
");

$query->bind_param("i", $employee_id);
$query->execute();
$payroll = $query->get_result();

$rows = [];
while ($row = $payroll->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
?>
