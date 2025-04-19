<?php
include 'db.php';

$sql = "SELECT Employee_ID, Name, Position FROM Employee_info";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['Employee_ID']}</td>
                <td>{$row['Name']}</td>
                <td>{$row['Position']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No employees found.</td></tr>";
}

$conn->close();
?>
