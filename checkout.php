<?php
session_start();

// Get cart data from POST (hidden field) or from session if available
$cartData = [];
if (isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true);
} elseif (isset($_SESSION['cart'])) {
    $cartData = $_SESSION['cart'];
}

// ✅ Normalize 'id' to 'product_id' for each item
foreach ($cartData as &$item) {
    if (isset($item['id'])) {
        $item['product_id'] = $item['id'];
        unset($item['id']);
    }
}
unset($item); // good practice after using reference

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <style>
    /* [Same CSS as before — unchanged] */
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fff;
      color: #333;
      padding: 30px;
    }
    h1 {
      color: #ff1774;
      text-align: center;
      margin-bottom: 20px;
    }
    .checkout-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      max-width: 1100px;
      margin: auto;
    }
    .cart-summary, .billing-form {
      flex: 1 1 48%;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .product {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
    }
    .product img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }
    .product-info h3 {
      margin: 0;
      font-size: 18px;
    }
    .product-info p {
      margin: 5px 0;
      color: #555;
    }
    .total {
      text-align: right;
      font-size: 20px;
      font-weight: bold;
      margin-top: 20px;
      color: #000;
    }
    .billing-form h2 {
      color: #ff1774;
      margin-bottom: 15px;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .payment-options {
      margin-top: 20px;
    }
    .payment-method {
      margin-bottom: 10px;
    }
    .upi-section, .scanner-section {
      display: none;
      padding: 10px;
      background: #fff0f6;
      border: 1px dashed #ff1774;
      border-radius: 8px;
      text-align: center;
      margin-top: 10px;
    }
    .upi-section input {
      width: 60%;
      margin-top: 10px;
    }
    .scanner-section img {
      width: 150px;
      margin-top: 10px;
    }
    button {
      background-color: #ff1774;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
    }
  </style>
  <script>
    function togglePaymentOptions() {
      const method = document.querySelector('input[name="payment"]:checked').value;
      document.querySelector('.upi-section').style.display = (method === 'upi') ? 'block' : 'none';
      document.querySelector('.scanner-section').style.display = (method === 'qr') ? 'block' : 'none';
    }
    window.onload = function(){
      togglePaymentOptions();
    }
  </script>
</head>
<body>
  <h1>Checkout</h1>
  <div class="checkout-container">
    <!-- Cart Summary -->
    <div class="cart-summary">
      <?php if (empty($cartData)): ?>
        <p class="empty">Your cart is empty!</p>
      <?php else: ?>
        <?php foreach ($cartData as $item): 
          $itemTotal = $item['price'] * $item['quantity'];
          $total += $itemTotal;
        ?>
          <div class="product">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
            <div class="product-info">
              <h3><?php echo htmlspecialchars($item['name']); ?></h3>
              <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
              <p>Qty: <?php echo $item['quantity']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="total">Total: ₹<?php echo number_format($total, 2); ?></div>
      <?php endif; ?>
    </div>

    <!-- Billing & Shipping Form -->
    <div class="billing-form">
      <h2>Billing & Shipping Info</h2>
      <form action="place_order.php" method="post">
        <!-- ✅ Send corrected cart data -->
        <input type="hidden" name="cart_data" value='<?php echo htmlspecialchars(json_encode($cartData), ENT_QUOTES, 'UTF-8'); ?>'>

        <!-- For debugging if needed -->
        <!-- <pre><?php print_r($cartData); ?></pre> -->

        <!-- If user is logged in -->
        <?php if(isset($_SESSION['user_id'])): ?>
          <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <?php else: ?>
          <input type="hidden" name="user_id" value="0">
        <?php endif; ?>

        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <textarea name="address" placeholder="Shipping Address" required></textarea>

        <div class="payment-options">
          <label><strong>Payment Method:</strong></label><br>
          <div class="payment-method">
            <input type="radio" name="payment" value="cod" checked onclick="togglePaymentOptions()"> Cash on Delivery
          </div>
          <div class="payment-method">
            <input type="radio" name="payment" value="upi" onclick="togglePaymentOptions()"> UPI
          </div>
          <div class="payment-method">
            <input type="radio" name="payment" value="qr" onclick="togglePaymentOptions()"> QR Scanner
          </div>

          <div class="upi-section">
            <label>Enter UPI ID:</label>
            <input type="text" name="upi_id" placeholder="example@upi">
          </div>
          <div class="scanner-section">
            <label>Scan QR to Pay</label><br>
            <img src="./assets/qr.png" alt="QR Code">
          </div>
        </div>
        <button type="submit">Place Order</button>
      </form>
    </div>
  </div>
</body>
</html>
