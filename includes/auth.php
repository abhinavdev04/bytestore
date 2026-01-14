<?php
// -------------------------------------------------------------
// Simple authentication helper functions
// These are included on protected pages to make sure that only
// logged-in users can access them.
// -------------------------------------------------------------

// Check if a customer is logged in before showing customer-only pages
function checkCustomerLogin() {
    if (!isset($_SESSION['customer_id'])) {
        // If not logged in, send the user to the customer login page
        header("Location: login.php");
        exit();
    }
}

// Check if an employee (staff) is logged in before showing staff pages
function checkEmployeeLogin() {
    if (!isset($_SESSION['employee_id'])) {
        // If not logged in, send the user to the employee login page
        header("Location: login.php");
        exit();
    }
}

// Note:
// - There is no separate admin role anymore.
// - Employees now act as the only staff role and can manage
//   products, customers, employees, and orders.
?>


