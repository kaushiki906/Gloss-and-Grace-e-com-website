<?php
// navbar.php
// session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gloss & Grace</title>
  <link rel="stylesheet" href="front_page.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="front_page.css">
  <link rel="stylesheet" href="login_page.css">
</head>
<body>


<div class="navbar">

<div class="logo">
  <h3>Gloss& Grace</h3>
</div>

<div class="menus">
  <ul>
    <li><a href="#">HOME</a></li>
    <li><a href="#abouts">ABOUT</a></li>
    <li><a href="skincare_page.html">SKINCARE</a></li>
    <li><a href="makeup_page.html">MAKEUP</a></li>
    <li><a href="product_page.php">PRODUCT</a></li>
  </ul>
</div>

<div class="icons">
  <a href="javascript:void(0);" onclick="toggleSearchBar()">
    <i class="fa-solid fa-magnifying-glass" title="Search"></i>
  </a>

  <a href="#" onclick="toggleLikedPanel()">
    <i class="fa-solid fa-heart" title="Wishlist"></i>
  </a>

  <!-- Cart Icon with Dynamic Count -->
  <div id="cart-icon" style="cursor: pointer;">
    <i class="fa fa-bag-shopping"></i>
    <!-- <span id="cart-count">0</span> -->
  </div>

  <?php if (isset($_SESSION['username'])): ?>
    <div class="dropdown">
      <button class="dropbtn"><?php echo $_SESSION['username']; ?> ▼</button>
      <div class="dropdown-content">
        <?php if ($_SESSION['is_admin']): ?>
          <a href="admin.php">Admin Panel</a>
        <?php else: ?>
          <a href="profile.php">Profile</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  <?php else: ?>
    <a href="#" onclick="openModal()" class="user-icon"><i class="fas fa-user"></i></a>
  <?php endif; ?>
</div>

<style>
  .user-icon-wrapper {
    display: flex;
    align-items: center;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.3s ease;
  }

  .user-icon-wrapper:hover {
    background-color: #ffe4f0;
  }

  .dropbtn {
    background-color: #ff1774;
    color: white;
    padding: 8px 14px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    border-radius: 20px;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 160px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    right: 0;
    border-radius: 10px;
    /* overflow: hidden; */
  }

  .dropdown-content a {
    color: black;
    padding: 10px 16px;
    text-decoration: none;
    display: block;
    transition: background 0.3s ease;
  }

  .dropdown-content a:hover {
    background-color: #ffe4f0;
  }

  .dropdown:hover .dropdown-content {
    display: block;
  }

  .dropdown:hover .dropbtn {
    background-color: #e71368;
  }
</style>

<!-- Search Overlay -->
<div class="search-overlay" id="searchOverlay">
  <div class="search-box" id="searchBox">
    <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterProducts()" />
    <button class="close-btn" onclick="toggleSearchBar()">&times;</button>
  </div>
</div>
</div>

<!-- Liked Items Panel -->
<div id="liked-panel" class="liked-panel">
<button id="close-panel">Close ✖</button>
<h2 class="head">Your Wishlist</h2>
<div id="liked-items"></div>
</div>

<!-- JS for Like Icon with localStorage -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const likeButtons = document.querySelectorAll(".like-btn");
  const likedItemsContainer = document.getElementById("liked-items");
  const likedPanel = document.getElementById("liked-panel");

  let likedItems = JSON.parse(localStorage.getItem("likedItems")) || [];

  likedItems.forEach(item => {
    addLikedItem(item.id, item.name, item.price, item.image, false);
    const likeBtn = document.querySelector(`[data-id='${item.id}'] .like-icon`);
    if (likeBtn) likeBtn.classList.add("liked");
  });

  toggleEmptyMessage();

  likeButtons.forEach(button => {
    button.addEventListener("click", function() {
      const product = this.closest("[data-id]");
      const productId = product.dataset.id;
      const productName = product.dataset.name;
      const productPrice = product.dataset.price;
      const productImage = product.dataset.image;
      const icon = this.querySelector(".like-icon");

      if (icon.classList.contains("liked")) {
        icon.classList.remove("liked");
        removeLikedItem(productId);
      } else {
        icon.classList.add("liked");
        addLikedItem(productId, productName, productPrice, productImage, true);
      }
      toggleEmptyMessage();
    });
  });

  function addLikedItem(id, name, price, image, save = true) {
    if (!document.querySelector(`.liked-item[data-id='${id}']`)) {
      const item = document.createElement("div");
      item.classList.add("liked-item");
      item.setAttribute("data-id", id);
      item.innerHTML = `
        <button class="remove-btn" onclick="removeLikedItem('${id}')">✖</button>
        <img src="${image}" alt="${name}">
        <div class="liked-details">
          <p>${name}</p>
          <p class="price">$${price}</p>
        </div>
      `;
      likedItemsContainer.appendChild(item);

      if (save) {
        likedItems.push({
          id,
          name,
          price,
          image
        });
        localStorage.setItem("likedItems", JSON.stringify(likedItems));
      }
    }
  }

  window.removeLikedItem = function(id) {
    const item = document.querySelector(`.liked-item[data-id='${id}']`);
    if (item) item.remove();

    likedItems = likedItems.filter(item => item.id !== id);
    localStorage.setItem("likedItems", JSON.stringify(likedItems));

    const likeButton = document.querySelector(`[data-id='${id}'] .like-icon`);
    if (likeButton) likeButton.classList.remove("liked");

    toggleEmptyMessage();
  };
  
  function toggleEmptyMessage() {
    let emptyMsg = document.getElementById("empty-msg");
    if (!emptyMsg) {
      emptyMsg = document.createElement("p");
      emptyMsg.id = "empty-msg";
      emptyMsg.textContent = "No items in your wishlist.";
      emptyMsg.style.textAlign = "center";
      emptyMsg.style.color = "#999";
      likedItemsContainer.appendChild(emptyMsg);
    }
    emptyMsg.style.display = likedItems.length === 0 ? "block" : "none";
  }

  window.toggleLikedPanel = function() {
    likedPanel.classList.toggle("active");
  };

  const closePanelBtn = document.getElementById("close-panel");
  if (closePanelBtn) {
    closePanelBtn.addEventListener("click", function() {
      likedPanel.classList.remove("active");
    });
  }
});
</script>

<div id="cart-panel" class="cart-panel">
<button id="close-cart">X</button>
<h2>Shopping Cart</h2>
<div id="cart-items"></div>
<h3 id="cart-total">Total: $0.00</h3>
<button class="btn" onclick="checkout()">Checkout</button>
</div>

<script src="cart.js"></script>

<!-- cart script -->
<script>
document.querySelector(".fa-bag-shopping").addEventListener("click", function(event) {
  event.preventDefault(); // Prevent any default behavior (like jumping)
  document.getElementById("cart-panel").classList.add("active");
  displayCartItems(); // Update cart panel items when opened
});
</script>


<!-- Modal -->
<div id="loginModal" class="modal">
<div class="modal-content">
  <span class="close" onclick="closeModal()">&times;</span>

  <!-- Tab Buttons -->
  <div class="tab-buttons">
    <button class="active" onclick="showForm('login')">Login</button>
    <button onclick="showForm('register')">Register</button>
  </div>

  <!-- Login Form -->
  <form id="loginForm" class="form-container active" method="POST" action="login_process.php">
    <h2>Login</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>

  <!-- Register Form -->
  <form id="registerForm" class="form-container" method="POST" action="register_process.php">
    <h2>Register</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>
</div>
</div>

<script src="login_page.js"></script>


<!-- search filter -->
<script>
function filterProducts() {
  const input = document.getElementById('searchInput').value.toLowerCase();

  // Explore products
  document.querySelectorAll('.product').forEach(product => {
    const name = product.getAttribute('data-name').toLowerCase();
    product.style.display = name.includes(input) ? '' : 'none';
  });

  // Hard to Resist deals
  document.querySelectorAll('.deal').forEach(deal => {
    const name = deal.getAttribute('data-name').toLowerCase();
    deal.style.display = name.includes(input) ? '' : 'none';
  });
}
</script>

</body>
</html>