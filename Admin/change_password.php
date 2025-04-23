<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->new_password)) {
        echo json_encode(["status" => "error", "message" => "Missing new password"]);
        exit();
    }

    $new_password = $data->new_password;
    $employee_username = $data->employee_username ?? null;
    $current_password = $data->current_password ?? null;

    if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit();
    }

    $username = $_SESSION['username'];
    $role = $_SESSION['role'];

    if ($role === 'Admin' && $employee_username) {
        // Admin updating an employee's password (no current password needed)
        $update_sql = "UPDATE employee_user SET Password = '" . password_hash($new_password, PASSWORD_DEFAULT) . "' WHERE Username = '$employee_username'";
        if ($conn->query($update_sql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Employee password updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating employee password: " . $conn->error]);
        }
        exit();
    }

    // Otherwise, user (or admin updating their own password)
    $sql = "SELECT Password FROM employee_user WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($current_password, $user['Password'])) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE employee_user SET Password = '$new_hashed' WHERE Username = '$username'";
            if ($conn->query($update_sql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error updating password: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
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
    <h2>Change Password</h2>
    <form id="changePasswordForm" autocomplete="off">
        <input type="text" name="fakeusernameremembered" style="display:none">
        <input type="password" name="fakepasswordremembered" style="display:none">

        <?php if ($_SESSION['role'] === 'Admin') { ?>
        <div class="form-group">
            <label>Change password for:</label>
            <select id="changeType">
                <option value="self">Myself</option>
                <option value="employee">Employee</option>
            </select>
        </div>
        <?php } ?>

        <div class="form-group" id="currentPasswordGroup">
            <label for="currentPassword">Current Password:</label>
            <input type="password" id="currentPassword">
        </div>

        <?php if ($_SESSION['role'] === 'Admin') { ?>
        <div class="form-group" id="employeeSelectGroup" style="display:none;">
            <label for="employee">Select Employee:</label>
            <select id="employee">
                <option value="">--Select Employee--</option>
                <?php
                $employee_sql = "SELECT Username FROM employee_user WHERE Role = 'Employee'";
                $employee_result = $conn->query($employee_sql);
                while ($employee = $employee_result->fetch_assoc()) {
                    echo "<option value='" . $employee['Username'] . "'>" . $employee['Username'] . "</option>";
                }
                ?>
            </select>
        </div>
        <?php } ?>
        
        <div class="form-group">
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" required>
        </div>

        <button type="submit">Change Password</button>
    </form>
</div>

<script>
    const form = document.getElementById("changePasswordForm");
    const changeType = document.getElementById("changeType");
    const currentPasswordGroup = document.getElementById("currentPasswordGroup");
    const employeeSelectGroup = document.getElementById("employeeSelectGroup");

    if (changeType) {
        changeType.addEventListener("change", () => {
            const isEmployee = changeType.value === "employee";
            currentPasswordGroup.style.display = isEmployee ? "none" : "block";
            employeeSelectGroup.style.display = isEmployee ? "block" : "none";
        });
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const isEmployee = changeType && changeType.value === "employee";
        const data = {
            current_password: isEmployee ? null : document.getElementById("currentPassword").value,
            new_password: document.getElementById("newPassword").value,
            employee_username: isEmployee ? document.getElementById("employee").value : null
        };

        fetch("change_password.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            alert(response.message);
            if (response.status === "success") {
                form.reset();
                if (changeType) {
                    changeType.value = "self";
                    currentPasswordGroup.style.display = "block";
                    employeeSelectGroup.style.display = "none";
                }
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




