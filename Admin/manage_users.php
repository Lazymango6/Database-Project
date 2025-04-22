<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include '../db.php';
session_start();

// Handle OPTIONS request (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Ensure the user is logged in as Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["status" => "error", "message" => "You do not have permission to access this page."]);
    exit();
}

// Handle actions based on the POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->action)) {
        // Edit user role
        if ($data->action == 'edit') {
            if (!isset($data->username) || !isset($data->role)) {
                echo json_encode(["status" => "error", "message" => "Missing input"]);
                exit();
            }

            $username = $data->username;
            $new_role = $data->role;

            // Update the user role
            $sql = "UPDATE employee_user SET Role = '$new_role' WHERE Username = '$username'";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "User updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error updating user: " . $conn->error]);
            }
        }

        // Delete user
        if ($data->action == 'delete') {
            if (!isset($data->username)) {
                echo json_encode(["status" => "error", "message" => "Missing input"]);
                exit();
            }

            $username = $data->username;

            // Delete the user from the database
            $sql = "DELETE FROM employee_user WHERE Username = '$username'";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error deleting user: " . $conn->error]);
            }
        }
    }
    exit();
}

// Fetch all users from the database
$sql = "SELECT * FROM employee_user";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    exit();
}

$users = [];
while ($user = $result->fetch_assoc()) {
    $users[] = $user;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        /* Styles for the page */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        button {
            padding: 8px 16px;
            background-color: #003399;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #002366;
        }
    </style>
</head>
<body>

    <h2>Manage Users</h2>

    <table id="usersTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- User rows will be inserted here dynamically -->
            <?php foreach ($users as $user): ?>
                <tr id="user-<?php echo $user['Username']; ?>">
                    <td><?php echo $user['Username']; ?></td>
                    <td>
                        <select onchange="editUserRole('<?php echo $user['Username']; ?>', this.value)">
                            <option value="Employee" <?php echo $user['Role'] === 'Employee' ? 'selected' : ''; ?>>Employee</option>
                            <option value="Manager" <?php echo $user['Role'] === 'Manager' ? 'selected' : ''; ?>>Manager</option>
                            <option value="Admin" <?php echo $user['Role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </td>
                    <td>
                        <button onclick="deleteUser('<?php echo $user['Username']; ?>')">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit user role
        function editUserRole(username, newRole) {
            fetch('manage_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'edit', username: username, role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('User role updated successfully');
                } else {
                    alert('Error updating user role: ' + data.message);
                }
            })
            .catch(error => console.error('Error updating user role:', error));
        }

        // Delete a user
        function deleteUser(username) {
            if (confirm('Are you sure you want to delete this user?')) {
                fetch('manage_users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', username: username })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('user-' + username).remove();
                        alert('User deleted successfully');
                    } else {
                        alert('Error deleting user: ' + data.message);
                    }
                })
                .catch(error => console.error('Error deleting user:', error));
            }
        }
    </script>

</body>
</html>
