<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("Access denied. Admins only.");
}

// Fetch payroll records with employee usernames
$sql =  "SELECT ep.*, eu.Username 
        FROM Employee_Payroll ep 
        JOIN employee_user eu ON ep.Employee_ID = eu.User_ID
        ORDER BY ep.Employee_ID DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Payroll History</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f6f8;
            padding: 30px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }
        .search-box {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-box input {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #003366;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #fbbc05;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background-color: #e0a800;
        }
        p {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payroll History</h2>

    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search for a username...">
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table id="payrollTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Pay Period Start</th>
                    <th>Pay Period End</th>
                    <th>Hourly Rate</th>
                    <th>Base Pay</th>
                    <th>Overtime Pay</th>
                    <th>Taxes</th>
                    <th>Deductions</th>
                    <th>Bonus</th>
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Username']) ?></td>
                        <td><?= htmlspecialchars($row['Pay_Period_Start']) ?></td>
                        <td><?= htmlspecialchars($row['Pay_Period_End']) ?></td>
                        <td>$<?= number_format($row['Hourly_Rate'], 2) ?></td>
                        <td>$<?= number_format($row['BasePay'], 2) ?></td>
                        <td>$<?= number_format($row['Overtime_Pay'], 2) ?></td>
                        <td>$<?= number_format($row['Taxes'], 2) ?></td>
                        <td>$<?= number_format($row['Deductions'], 2) ?></td>
                        <td>$<?= number_format($row['Bonus'], 2) ?></td>
                        <td><strong>$<?= number_format($row['NetPay'], 2) ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payroll records found.</p>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="../Admin/admin.html" class="back-btn">← Back to Admin Dashboard</a>
    </div>
</div>

<script>
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("payrollTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) { // Start at 1 to skip the header
        let td = tr[i].getElementsByTagName("td")[0]; // Username is in the first column
        if (td) {
            let textValue = td.textContent || td.innerText;
            if (textValue.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>

</body>
</html>
