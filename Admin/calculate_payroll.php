<?php
session_start();
require_once '../db.php';


// Fetch employee data for the dropdown
$sql = "SELECT Employee_ID, Name, Position FROM Employee_info";
$res = $conn->query($sql);

// Check if the query was successful and there are employees in the database
if (!$res) {
    echo "Error fetching employee data: " . $conn->error;
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $employee_id = intval($_POST['employee_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $hours_worked = floatval($_POST['hours_worked']);
    $overtime_hours = floatval($_POST['overtime_hours']);
    $deductions = floatval($_POST['deductions']);
    $bonus = floatval($_POST['bonus']);

    // Fetch employee pay info
    $stmt = $conn->prepare("SELECT Hourly_Rate, Tax_Rate FROM Employee_info WHERE Employee_ID = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $empData = $stmt->get_result()->fetch_assoc();

    if (!$empData) {
        echo "Error: Employee data not found.";
        exit;
    }

    $hourly_rate = $empData['Hourly_Rate'];
    $tax_rate = $empData['Tax_Rate'];

    // Calculate pay
    $base_pay = $hours_worked * $hourly_rate;
    $overtime_pay = $overtime_hours * $hourly_rate * 1.5;
    $taxes = ($base_pay + $overtime_pay + $bonus) * ($tax_rate / 100);
    $net_pay = ($base_pay + $overtime_pay + $bonus) - $taxes - $deductions;

    // Insert into Time_Tracking
    $stmt = $conn->prepare("INSERT INTO Time_Tracking (Employee_ID, Pay_Period_Start, Pay_Period_End, Hours_Worked, Overtime_Hours)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $employee_id, $start_date, $end_date, $hours_worked, $overtime_hours);
    if (!$stmt->execute()) {
        echo "Error inserting into Time_Tracking: " . $stmt->error;
        exit;
    }

    // Insert into Employee_Payroll
    $stmt = $conn->prepare("INSERT INTO Employee_Payroll (Employee_ID, Pay_Period_Start, Pay_Period_End, Hourly_Rate, BasePay, Taxes, Overtime_Pay, Deductions, Bonus, NetPay)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddddddd", $employee_id, $start_date, $end_date, $hourly_rate, $base_pay, $taxes, $overtime_pay, $deductions, $bonus, $net_pay);
    if (!$stmt->execute()) {
        echo "Error inserting into Employee_Payroll: " . $stmt->error;
        exit;
    }

    echo "<p>✅ Payroll added successfully for Employee ID: $employee_id</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payroll</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            background: linear-gradient(145deg, #ffffff, #f7f9fc);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 420px;
        }
        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 25px;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }
        form input[type="date"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f5f7fa;
        }
        form input[type="submit"] {
            width: 100%;
            background-color: #0055a5;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        form input[type="submit"]:hover {
            background-color: #003f7f;
        }
        .back-btn {
            display: inline-block;
            margin-top: 18px;
            text-align: center;
            width: 100%;
            text-decoration: none;
            background: #fbbc05;
            color: #333;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Payroll Record</h2>
    <form method="POST">
        <label>Employee:</label>
        <select name="employee_id" required>
            <?php
            // Populate dropdown with employee data
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['Employee_ID']}'>{$row['Name']} ({$row['Position']})</option>";
            }
            ?>
        </select>

        <label>Pay Period Start:</label>
        <input type="date" name="start_date" required>

        <label>Pay Period End:</label>
        <input type="date" name="end_date" required>

        <label>Hours Worked:</label>
        <input type="number" name="hours_worked" step="0.01" required>

        <label>Overtime Hours:</label>
        <input type="number" name="overtime_hours" step="0.01" value="0">

        <label>Deductions:</label>
        <input type="number" name="deductions" step="0.01" value="0">

        <label>Bonus:</label>
        <input type="number" name="bonus" step="0.01" value="0">

        <input type="submit" value="Add Payroll">
    </form>

    <a href="../Admin/admin.html" class="back-btn">← Back to Admin Dashboard</a>
</div>

</body>
</html>

