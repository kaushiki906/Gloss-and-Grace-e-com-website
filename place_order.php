<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check required fields
    if (!isset($_POST['user_id'], $_POST['name'], $_POST['email'], 
               $_POST['phone'], $_POST['address'], $_POST['payment'], 
               $_POST['cart_data'])) {
        echo "All fields are required!";
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment_method = trim($_POST['payment']);
    $upi_id = isset($_POST['upi_id']) ? trim($_POST['upi_id']) : '';

    $rawCartData = $_POST['cart_data'];
    $cartData = json_decode($rawCartData, true);

    if (!$cartData || !is_array($cartData)) {
        echo "Invalid cart data!";
        exit;
    }

    // Calculate total
    $total_amount = 0;
    foreach ($cartData as $item) {
        if (!isset($item['price'], $item['quantity'])) {
            echo "Invalid product format.";
            exit;
        }
        $total_amount += floatval($item['price']) * intval($item['quantity']);
    }

    $order_date = date('Y-m-d H:i:s');
    $txn_id = uniqid('TXN');
    $cart_json = json_encode($cartData);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $query = "INSERT INTO orders 
            (user_id, name, email, phone, address, payment_method, upi_id, 
             total_amount, order_date, cart_details, txn_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param("isssssdssss", $user_id, $name, $email, $phone, $address, 
                                         $payment_method, $upi_id, $total_amount, 
                                         $order_date, $cart_json, $txn_id);

        if (!$stmt->execute()) {
            throw new Exception("Order insert failed: " . $stmt->error);
        }

        $order_id = $stmt->insert_id;

        // Insert into order_items
        if (tableExists($conn, 'order_items')) {
            foreach ($cartData as $item) {
                $product_id = isset($item['product_id']) ? (int)$item['product_id'] : 0;
                $product_name = isset($item['name']) ? trim($item['name']) : null;
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];

                // If product_id is valid and exists in DB, insert as normal
                if ($product_id > 0 && productExists($conn, $product_id)) {
                    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price, product_name)
                                  VALUES (?, ?, ?, ?, NULL)";
                    $itemStmt = $conn->prepare($itemQuery);
                    $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                } else {
                    // Insert manually added product with product_name
                    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price, product_name)
                                  VALUES (?, NULL, ?, ?, ?)";
                    $itemStmt = $conn->prepare($itemQuery);
                    $itemStmt->bind_param("iids", $order_id, $quantity, $price, $product_name);
                }

                if (!$itemStmt->execute()) {
                    throw new Exception("Item insert failed: " . $itemStmt->error);
                }
                $itemStmt->close();
            }
        }

        $conn->commit();
        echo "✅ Order placed successfully! Order ID: " . $order_id;

    } catch (Exception $e) {
        $conn->rollback();
        echo "❌ Error: " . $e->getMessage();
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();

} else {
    echo "Invalid request!";
}

// Check if table exists
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

// Check if product_id exists in products table
function productExists($conn, $product_id) {
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}
?>
