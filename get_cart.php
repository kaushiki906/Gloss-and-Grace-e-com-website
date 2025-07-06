<?php
include "db_connect.php";

$query = "SELECT products.id, products.name, products.price, products.image, cart.quantity 
          FROM cart 
          INNER JOIN products ON cart.product_id = products.id";

$result = $conn->query($query);
$cartItems = [];

while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "price" => $row["price"],
        "image" => $row["image"],
        "quantity" => $row["quantity"]
    ];
}

echo json_encode($cartItems);
?>
