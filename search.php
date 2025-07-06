<?php
$conn = new mysqli("localhost", "root", "", "gloss_and_grace");
$q = $_GET['q'] ?? '';
$q = $conn->real_escape_string($q);

$result = $conn->query("SELECT * FROM products WHERE name LIKE '%$q%' LIMIT 10");

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "
    <div class='product-result' style='margin-bottom:20px;border-bottom:1px solid #eee;padding-bottom:10px;'>
      <img src='uploads/{$row['image']}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;margin-right:10px;float:left;'>
      <div style='overflow:hidden;'>
        <strong>{$row['name']}</strong><br>
        ₹{$row['price']}<br>
        <a href='product_page.php?id={$row['id']}' style='color:#ff1774;'>View Product</a>
      </div>
    </div>
    ";
  }
} else {
  echo "<p style='text-align:center;color:#999;'>No products found.</p>";
}
?>
