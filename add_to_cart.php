<?php
include "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    foreach ($data as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $query = "INSERT INTO cart (product_id, quantity) VALUES ($product_id, $quantity)";
        $conn->query($query);
    }
    echo json_encode(["status" => "success", "message" => "Cart saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
}
?>
