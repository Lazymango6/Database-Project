<?php
include 'db_connection.php';

$query = "SELECT * FROM employees"; // Assuming an 'employees' table exists
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #04205f; color: white; }
    </style>
</head>
<body>
    <h2>Employee List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['email']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
