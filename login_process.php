<?php
session_start();
include 'db_connection.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password']; // don't escape password — we'll use it raw in password_verify()

$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Check hashed password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];

        echo "<script>window.location.href='front_page.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid password'); window.location.href='front_page.php';</script>";
    }
} else {
    echo "<script>alert('User not found'); window.location.href='front_page.php';</script>";
}
?>
