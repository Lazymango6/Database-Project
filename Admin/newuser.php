<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    include '../db.php';

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->username, $data->password, $data->role)) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $username = $conn->real_escape_string($data->username);
    $password = password_hash($data->password, PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($data->role);

    $validRoles = ['Employee', 'Manager', 'Admin'];
    if (!in_array($role, $validRoles)) {
        echo json_encode(["status" => "error", "message" => "Invalid role"]);
        exit;
    }

    $conn->begin_transaction();

    try {
        $employee_id = null;

        if ($role === 'Admin') {
            // Step 1: Create minimal employee_info entry for admin
            $stmt1 = $conn->prepare("INSERT INTO employee_info (Name) VALUES (?)");
            $stmt1->bind_param("s", $username);  // Using username as the Name
            $stmt1->execute();
            $employee_id = $conn->insert_id;
        }

        // Step 2: Create the user and link employee_id if available
        if ($employee_id !== null) {
            $stmt2 = $conn->prepare("INSERT INTO employee_user (Username, Password, Role, Employee_ID) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("sssi", $username, $password, $role, $employee_id);
        } else {
            $stmt2 = $conn->prepare("INSERT INTO employee_user (Username, Password, Role) VALUES (?, ?, ?)");
            $stmt2->bind_param("sss", $username, $password, $role);
        }

        $stmt2->execute();

        $conn->commit();

        echo json_encode(["status" => "success", "message" => "User created successfully" . ($employee_id ? " with Employee_ID $employee_id" : "")]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            background: white;
            margin: 30px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #003399;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #002366;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create User Account</h2>
        <form id="createUserForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" required>
                    <option value="">Select a role</option>
                    <option value="Admin">Admin</option>
                    <option value="Employee">Employee</option>
                </select>
            </div>
            <button type="submit">Create User</button>
        </form>
    </div>

    <script>
        document.getElementById("createUserForm").addEventListener("submit", function (e) {
            e.preventDefault();

            const data = {
                username: document.getElementById("username").value.trim(),
                password: document.getElementById("password").value,
                role: document.getElementById("role").value
            };

            fetch("newuser.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === "success") {
                    alert("User created successfully!");
                    document.getElementById("createUserForm").reset();
                } else {
                    alert("Error: " + response.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Something went wrong.");
            });
        });
    </script>
</body>
</html>
