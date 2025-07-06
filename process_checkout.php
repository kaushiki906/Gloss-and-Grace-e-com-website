<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];

    // You can save the order details to the database or send an email here
    // For now, we will just display the submitted data:

    echo "<h1>Order Confirmation</h1>";
    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Address:</strong> $address</p>";
    echo "<p><strong>Payment Method:</strong> $payment_method</p>";

    // You can redirect to a "Thank You" page or process the payment here
    // For example:
    // header('Location: thank_you.php');
}
?>
