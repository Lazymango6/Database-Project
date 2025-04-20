fetch("http://localhost/PayCalc/Employee/add_employee.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
})
.then(res => res.text()) // Change from .json() to .text()
.then(text => {
    console.log("Raw response:", text);
    try {
        const response = JSON.parse(text);
        if (response.status === "success") {
            alert("Employee added successfully!");
            document.getElementById("addEmployeeForm").reset();
        } else {
            alert("Error: " + response.message);
        }
    } catch (e) {
        console.error("JSON parsing failed:", e);
        alert("Invalid server response. Check console.");
    }
})
.catch(error => {
    console.error("Error:", error);
    alert("Something went wrong!");
});
