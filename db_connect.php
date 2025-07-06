<?php
$Server = 'localhost';
$user = 'root';
$password = '';
$db = 'cart_db';

$conn = mysqli_connect($Server, $user, $password, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>