<?php 
$Server = 'localhost';
$user = 'root';
$password = '';
$db = 'glossandgrace';
$conn = mysqli_connect($Server,$user,$password,$db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    echo "Connected to database";
}

?>
