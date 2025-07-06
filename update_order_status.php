<?php
// Include database connection
include('db_connection.php'); // Include your database connection file

// Check if form data is submitted
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    // Sanitize and retrieve the form data
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Validate or sanitize input if needed (for security)
    // You can use mysqli_real_escape_string or prepared statements to prevent SQL injection

    // Update query to change the status of the order
    $query = "UPDATE orders SET status = '$status' WHERE order_id = '$orderId'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Redirect back to the orders page after updating status
        header("Location: admin_panel.php?section=orders");  // Redirect to orders section
        exit;
    } else {
        // Error handling in case the query fails
        echo "Error updating status: " . mysqli_error($conn);
    }
}
?>
