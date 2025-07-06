<?php
session_start();
include 'db_connection.php';

// Get and sanitize inputs
$username = mysqli_real_escape_string($conn, $_POST['username']); // match login field
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // securely hash the password

// Insert new user
$query = "INSERT INTO users (username, email, password, is_admin) VALUES ('$username', '$email', '$password', 0)";
$result = mysqli_query($conn, $query);

if ($result) {
    // Fetch the inserted user to get their ID
    $user_id = mysqli_insert_id($conn);

    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = 0;

    echo "<script>alert('Registered successfully!'); window.location.href='front_page.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
