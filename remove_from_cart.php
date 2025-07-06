<?php
include "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id'])) {
    $product_id = $data['id'];
    $query = "DELETE FROM cart WHERE product_id = $product_id";
    $conn->query($query);
    echo json_encode(["status" => "success", "message" => "Item removed"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid item"]);
}
?>
