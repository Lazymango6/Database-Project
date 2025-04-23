<?php
include '../db.php';

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f4f4;
            padding: 20px;
        }
        .header {
            background: #ba1a1a;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
        }
        .nav {
            display: flex;
            justify-content: center;
            background: #78150c;
            padding: 10px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: bold;
        }
        .nav a:hover {
            background: #0044cc;
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            margin: 30px auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #002366;
            border-bottom: 3px solid #003399;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #003399;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">Employee List</div>
    <div class="nav">
        <a href="../main.html">Home</a>
        <a href="../Admin/admin.html">Admin Dashboard</a>
        <a href="../Employee/add_employee.html">Add Employee</a>
        <a href="#">Payroll Calculation</a>
    </div>
    
    <div class="container">
        <h2>Employee Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Employee_ID, Name, Position FROM employee_info";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['Employee_ID']) . "</td>
                                <td>" . htmlspecialchars($row['Name']) . "</td>
                                <td>" . htmlspecialchars($row['Position']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No employees found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
