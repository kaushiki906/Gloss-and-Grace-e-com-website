<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gloss_and_grace");
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html>

<head>
  <title>Our Products - Gloss & Grace</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="product_page.css">
</head>

<body>

  <h1>Latest Products</h1>

  <div class="product-grid">
    <?php while ($p = $products->fetch_assoc()): ?>
      <div class="product"
        data-id="<?= $p['id'] ?>"
        data-name="<?= htmlspecialchars($p['name']) ?>"
        data-price="<?= $p['price'] ?>"
        data-image="uploads/<?= $p['image'] ?>">

        <!-- Heart Icon -->
        <i class="fa-solid fa-heart like-icon"></i>

        <img src="uploads/<?= $p['image'] ?>" alt="<?= $p['name'] ?>">

        <div class="details">
          <h3><?= $p['name'] ?></h3>
          <p><?= substr($p['description'], 0, 80); ?>...</p>
          <p class="price">₹<?= $p['price'] ?></p>
          <button class="add-to-cart">Add to Cart</button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- Load your main cart system -->
  <script src="cart.js"></script>

  <script>
    const likedItems = JSON.parse(localStorage.getItem("likedItems")) || [];

    document.querySelectorAll(".product").forEach(product => {
      const likeIcon = product.querySelector(".like-icon");

      const id = product.dataset.id;
      const name = product.dataset.name;
      const price = product.dataset.price;
      const image = product.dataset.image;

      if (likedItems.some(item => item.id === id)) {
        likeIcon.classList.add("liked");
      }

      likeIcon.addEventListener("click", () => {
        let liked = JSON.parse(localStorage.getItem("likedItems")) || [];
        const exists = liked.find(item => item.id === id);

        if (exists) {
          liked = liked.filter(item => item.id !== id);
          likeIcon.classList.remove("liked");
        } else {
          liked.push({
            id,
            name,
            price,
            image
          });
          likeIcon.classList.add("liked");
        }

        localStorage.setItem("likedItems", JSON.stringify(liked));
        updateLikedPanel(); // live update
      });
    });

    function updateLikedPanel() {
      const likedItems = JSON.parse(localStorage.getItem("likedItems")) || [];
      const panel = document.getElementById("liked-items");
      panel.innerHTML = "";

      if (likedItems.length === 0) {
        panel.innerHTML = "<p style='text-align:center;color:#999;'>No items in your wishlist.</p>";
        return;
      }

      likedItems.forEach(item => {
        const div = document.createElement("div");
        div.classList.add("liked-item");
        div.setAttribute("data-id", item.id);
        div.innerHTML = ` 
        <button class="remove-btn" onclick="removeLikedItem('${item.id}')">✖</button>
        <img src="${item.image}" alt="${item.name}">
        <div class="liked-details">
          <p>${item.name}</p>
          <p class="price">₹${item.price}</p>
        </div>
      `;
        panel.appendChild(div);
      });
    }
  </script>

</body>
</html>