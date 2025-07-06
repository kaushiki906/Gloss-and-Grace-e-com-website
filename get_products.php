<?php
include "db_connect.php";

$query = "SELECT * FROM products";
$result = $conn->query($query);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "price" => $row["price"],
        "image" => $row["image"]  // Ensure the image column is set correctly
    ];
}

echo json_encode($products);
?>
