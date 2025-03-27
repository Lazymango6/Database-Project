<form action="add_employee.php" method="POST">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="department">Department:</label>
    <input type="text" id="department" name="department" required>

    <label for="position">Position:</label>
    <input type="text" id="position" name="position" required>

    <label for="salary">Salary:</label>
    <input type="number" id="salary" name="salary" step="0.01" required>

    <label for="hourly_rate">Hourly Rate:</label>
    <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" required>

    <label for="tax_rate">Tax Rate:</label>
    <input type="number" id="tax_rate" name="tax_rate" step="0.01" required>

    <button type="submit">Add Employee</button>
</form>

