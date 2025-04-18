document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();
  
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
  
    fetch("http://localhost/PayCalc/Login/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ username, password })
      })
      
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          if (data.role === "Admin") {
            window.location.href = "../Admin/admin.html"; // Admin dashboard
          } else if (data.role === "Employee") {
            window.location.href = "../Employee/employee_dashboard.html"; // Employee dashboard
          }
        } else {
          alert(data.message); // Show error message
        }
      })
      .catch(err => {
        console.error("Login error:", err);
      });
});

  