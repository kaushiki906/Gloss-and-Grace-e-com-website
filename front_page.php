<?php
session_start();
include("db_connection.php"); 
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
  <link rel="stylesheet" href="login_page.css">
</head>

<body>

  <?php
  $latestBanner = $conn->query("SELECT * FROM banners ORDER BY id DESC LIMIT 1");
  if ($latestBanner->num_rows > 0) {
    $banner = $latestBanner->fetch_assoc();
    if ($banner['type'] == 'video') {
      echo "<video autoplay muted loop class='banner-video'><source src='uploads/{$banner['image']}' type='video/mp4'></video>";
    } else {
      echo "<img src='suploads/{$banner['image']}' class='banner-image'>";
    }
  }
  ?>

  <style>
    .banner-video {
      width: 100%;
      height: 100vh;
      object-fit: cover;
      display: block;
    }


    .banner-image {
      width: 100%;
      height: 100vh;
      max-height: 500px;
      object-fit: cover;
    }
  </style>

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
      <!-- Search Icon -->
      <a href="javascript:void(0);" onclick="toggleSearchBar()">
        <i class="fa-solid fa-magnifying-glass" title="Search"></i>
      </a>

      <!-- Search Overlay (Already in your main file) -->
      <div class="search-overlay" id="searchOverlay">
        <div class="search-box" id="searchBox">
          <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterProducts()" />
          <button class="close-btn" onclick="toggleSearchBar()">&times;</button>
        </div>
      </div>

      <a href="#" onclick="toggleLikedPanel()">
        <i class="fa-solid fa-heart" title="Wishlist"></i>
      </a>

      <!-- Cart Icon -->
      <div id="cart-icon" style="cursor: pointer;">
        <i class="fa fa-bag-shopping"></i>
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
    <button class="btn" onclick="sendCartToCheckout()">Checkout</button>
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

  <div class="about" id="abouts">
    <h1 class="heading"> <span>About</span> Us</h1>

    <div class="row">

      <div class="vd2">
        <video loop muted autoplay="autoplay">
          <source src="./assets/videos/video2.mp4" type="video/mp4">
        </video>
      </div>

      <div class="content">
        <h3>Why Choose Us?</h3>
        <p>At Gloss&Grace, we believe beauty is a reflection of confidence and self-care. Our commitment to
          enhancing your natural glow is what sets us apart. We offer a wide range of premium beauty products
          and services tailored to your unique needs, using only the highest-quality ingredients and
          innovative techniques. Our expert team is dedicated to providing personalized consultations,
          ensuring that every experience is not just a service, but a journey to feeling your absolute best.
          With Gloss&Grace, it's not just about looking beautiful; it's about embracing your true elegance
          with grace.</p>
      </div>
    </div>

    <div class="row2">

      <div class="content2">
        <h3>Our Newest Product</h3>
        <p>Introducing our newest relaxation product, the Serenity Glow – your ultimate escape to tranquility.
          Specially crafted to soothe both the body and mind, this luxurious blend of calming ingredients will
          transport you into a world of relaxation. Whether you’re unwinding after a long day or simply
          looking to indulge in some much-needed self-care, Serenity Glow helps melt away stress, leaving you
          with soft, radiant skin and a peaceful sense of calm. Treat yourself to this blissful experience and
          elevate your beauty routine to a moment of pure serenity.
        </p>
      </div>

      <div class="vd3">
        <video loop muted autoplay="autoplay">
          <source src="./assets/videos/video3.mp4" type="video/mp4">
        </video>
      </div>
    </div>

    <div class="row3">

      <div class="vd4">
        <video loop muted autoplay="autoplay">
          <source src="./assets/videos/video4.mp4" type="video/mp4">
        </video>
      </div>

      <div class="content3">
        <h3>Relax and Rejuvenate with Calm & Glow</h3>
        <p>Experience the ultimate in relaxation with our Calm & Glow Relaxing Body Lotion. This soothing
          formula is designed to deeply hydrate and nourish your skin, while the calming blend of essential
          oils promotes a serene, peaceful sensation. With every application, feel the gentle, stress-melting
          relief as it absorbs quickly, leaving your skin soft, smooth, and beautifully scented. Perfect for
          winding down after a long day or simply creating a tranquil moment in your routine, Calm & Glow
          provides a comforting escape that will leave you feeling refreshed and rejuvenated, both inside and
          out.
        </p>
      </div>
    </div>

    <div class="row4">

      <div class="content4">
        <h3>New Arrival: HydraGlow Hydrating Serum</h3>
        <p>Say hello to radiant, glowing skin with our HydraGlow Hydrating Serum. This lightweight,
          fast-absorbing formula is packed with powerful hydrating ingredients that lock in moisture, leaving
          your skin soft, plump, and refreshed. Ideal for all skin types, it works to restore your skin’s
          natural balance while giving you a smooth, dewy finish that lasts all day. Whether you’re looking
          for a daily boost or an extra dose of hydration, HydraGlow is the perfect addition to your skincare
          routine for that fresh, healthy glow.
        </p>
      </div>

      <div class="vd5">
        <video loop muted autoplay="autoplay">
          <source src="./assets/videos/video5.mp4" type="video/mp4">
        </video>
      </div>
    </div>

  </div>

  <div class="explore" id="explore">
    <h2><span>Explore</span> Our <span>Top</span> Brands</h2>
    <div id="product-list" class="product-container">
      <div class="product" data-id="1" data-name="Olay" data-price="15.99" data-image="./assets/top_brand/olay.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/olay.avif" alt="Olay">
        <p>Olay</p>
        <p>$15.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="2" data-name="Foxtale" data-price="12.49"
        data-image="./assets/top_brand/foxtale.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/foxtale.avif" alt="Foxtale">
        <p>Foxtale</p>
        <p>$12.49</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="3" data-name="Dot & Key" data-price="18.99"
        data-image="./assets/top_brand/dot&key.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/dot&key.avif" alt="Dot & Key">
        <p>Dot & Key</p>
        <p>$18.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="4" data-name="Dr. Sheths" data-price="20.99"
        data-image="./assets/top_brand/dr.sheths.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/dr.sheths.avif" alt="Dr. Sheths">
        <p>Dr. Sheths</p>
        <p>$20.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="5" data-name="aqualogica" data-price="22.78"
        data-image="./assets/top_brand/aqualogica.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/aqualogica.avif" alt="aqualogica">
        <p>aqualogica</p>
        <p>$22.78</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="6" data-name="Joseson" data-price="29.98"
        data-image="./assets/top_brand/joseon.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/joseon.avif" alt="Joseson">
        <p>Joseson</p>
        <p>$29.98</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="7" data-name="Milani" data-price="30.89"
        data-image="./assets/top_brand/milani.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/milani.avif" alt="Milani">
        <p>Milani</p>
        <p>$30.89</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="8" data-name="Ordinary" data-price="23.69"
        data-image="./assets/top_brand/ordinary.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/ordinary.avif" alt="Ordinary">
        <p>Ordinary</p>
        <p>$23.69</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="9" data-name="Simple" data-price="33.79"
        data-image="./assets/top_brand/simple.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/simple.avif" alt="Simple">
        <p>Simple</p>
        <p>$33.79</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="product" data-id="10" data-name="Pixi" data-price="38.99" data-image="./assets/top_brand/pixi.avif">
        <div class="like-btn"><i class="fas fa-heart like-icon"></i></div>
        <img src="./assets/top_brand/pixi.avif" alt="Pixi">
        <p>Pixi</p>
        <p>$38.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
    </div>
  </div>

  <div class="slid1">
    <!-- Heading Image -->
    <img class="heading-image" src="./assets/newslid/new slid.avif" alt="Heading Image">

    <!-- Slider Section (Only One Div for Images) -->
    <div class="slid" id="image-gallery">
      <img src="./assets/newslid/first.avif" alt="">
      <img src="./assets/newslid/second.avif" alt="">
      <img src="./assets/newslid/third.avif" alt="">
      <img src="./assets/newslid/forth.avif" alt="">
      <img src="./assets/newslid/fifth.avif" alt="">
      <img src="./assets/newslid/sixth.avif" alt="">
      <img src="./assets/newslid/seventh.avif" alt="">
      <img src="./assets/newslid/eight.avif" alt="">
      <img src="./assets/newslid/ninth.avif" alt="">
      <img src="./assets/newslid/tenth.avif" alt="">
      <img src="./assets/newslid/11th.avif" alt="">
    </div>
  </div>

  <div class="trending">
    <h1 class="heading2"><span>Beauty</span> To <span>Fall</span> For</h1>

    <div class="box2">
      <div class="row">
        <div class="image"><a href="skincare_page.html"><img src="./assets/girls/skincare_img.avif" alt=""></a>
        </div>
        <div class="image"><a href="makeup_page.html"><img src="./assets/girls/makeup_img.avif" alt=""></a>
        </div>
        <div class="image"><a href="haircare.html"><img src="./assets/girls/haircare_img.avif" alt=""></a></div>
      </div>
    </div>

  </div>

  <div class="galentine">
    <h1 class="heading3"><span>Beauty</span> Gift for Every<span>"Galentine"</span></h1>
    <div class="galen">
      <div class="imgs"><img src="./assets/galentine/benefit.avif" alt=""></div>
      <div class="imgs"><img src="./assets/galentine/caudaile.avif" alt=""></div>
      <div class="imgs"><img src="./assets/galentine/laneige.avif" alt=""></div>
      <div class="imgs"><img src="./assets/galentine/nudestix.avif" alt=""></div>
      <div class="imgs"><img src="./assets/galentine/nyveda.avif" alt=""></div>
      <div class="imgs"><img src="./assets/galentine/smashbox.avif" alt=""></div>
    </div>
  </div>

  <!-- galentine script -->
  <script>
    function autoScroll() {
      const container = document.querySelector(".galen");
      let scrollAmount = 0;
      const scrollStep = 300;
      const scrollMax = container.scrollWidth - container.clientWidth;

      setInterval(() => {
        if (scrollAmount >= scrollMax) {
          container.scrollTo({
            left: 0,
            behavior: "smooth"
          });
          scrollAmount = 0;
        } else {
          container.scrollBy({
            left: scrollStep,
            behavior: "smooth"
          });
          scrollAmount += scrollStep;
        }
      }, 2000);
    }

    document.addEventListener("DOMContentLoaded", autoScroll);
  </script>

  <!-- HTML for Hard to Resist Deals Section -->
  <section class="resist">
    <h1 class="heading4">Hard to <span>Resist</span> Deals</h1>
    <div class="box3">
      <!-- 20 Product Cards -->
      <div class="deal" data-id="cetaphil" data-name="cetaphil" data-price="25.99"
        data-image="./assets/resist/cetaphil.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/cetaphil.avif" alt="Nykaa">
        </div>
        <h3>Cetaphil</h3>
        <p>$25.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="charlotte tilbury" data-name="charlotte tilbury" data-price="14.99"
        data-image="./assets/resist/charlotte tilbury.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/charlotte tilbury.avif" alt="Mamaearth">
        </div>
        <h3>charlotte tilbury</h3>
        <p>$14.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="derma" data-name="derma" data-price="17.25" data-image="./assets/resist/derma.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/derma.avif" alt="Biotique">
        </div>
        <h3>derma</h3>
        <p>$17.25</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="dotandkey" data-name="dotandkey" data-price="19.75"
        data-image="./assets/resist/dotandkey.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/dotandkey.avif" alt="Garnier">
        </div>
        <h3>Dot and key</h3>
        <p>$19.75</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="dove" data-name="dove" data-price="24.95" data-image="./assets/resist/dove.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/dove.avif" alt="L'Oreal">
        </div>
        <h3>dove</h3>
        <p>$24.95</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="elf" data-name="elf" data-price="21.99" data-image="./assets/resist/elf.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/elf.avif" alt="Plum">
        </div>
        <h3>efl</h3>
        <p>$21.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="kay" data-name="kay" data-price="39.99" data-image="./assets/resist/kay.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/kay.avif" alt="Forest Essentials">
        </div>
        <h3>kay</h3>
        <p>$39.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="lakme" data-name="lakme" data-price="28.49" data-image="./assets/resist/lakme.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/lakme.avif" alt="Neutrogena">
        </div>
        <h3>lakme</h3>
        <p>$28.49</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="lipsticks" data-name="lipsticks" data-price="31.79"
        data-image="./assets/resist/lipsticks.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/lipsticks.avif" alt="Minimalist">
        </div>
        <h3>lipsticks</h3>
        <p>$31.79</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="L'oreal" data-name="L'oreal" data-price="27.89"
        data-image="./assets/resist/l'oreal.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/l'oreal.avif" alt="Good Vibes">
        </div>
        <h3>L'oreal</h3>
        <p>$27.89</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <!-- Add 10 More Products Below -->
      <div class="deal" data-id="loveandplanet" data-name="loveandplanet" data-price="19.49"
        data-image="./assets/resist/loveandplanet.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/loveandplanet.avif" alt="The Derma Co">
        </div>
        <h3>Love and planet</h3>
        <p>$19.49</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="mac" data-name="mac" data-price="15.29" data-image="./assets/resist/mac.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/mac.avif" alt="Pond's">
        </div>
        <h3>Mac</h3>
        <p>$15.29</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="minimalist" data-name="minimalist" data-price="16.95"
        data-image="./assets/resist/minimalist.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/minimalist.avif" alt="VLCC">
        </div>
        <h3> Minimalist</h3>
        <p>$16.95</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="neutrogena" data-name="neutrogena" data-price="23.89"
        data-image="./assets/resist/neutrogena.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/neutrogena.avif" alt="WOW Skin Science">
        </div>
        <h3>Neutrogena</h3>
        <p>$23.89</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="plum" data-name="plum" data-price="22.49" data-image="./assets/resist/plum.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/plum.avif" alt="Bella Vita">
        </div>
        <h3>Plum</h3>
        <p>$22.49</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="ponds" data-name="ponds" data-price="20.69" data-image="./assets/resist/ponds.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/ponds.avif" alt="ManCode">
        </div>
        <h3>ponds</h3>
        <p>$20.69</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="swiss beauty" data-name="swiss beauty" data-price="26.39"
        data-image="./assets/resist/swiss beauty.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/swiss beauty.avif" alt="MCaffeine">
        </div>
        <h3>Swiss beauty</h3>
        <p>$26.39</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="tresemme" data-name="tresemme" data-price="18.99"
        data-image="./assets/resist/tresemme.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/tresemme.avif" alt="Joy">
        </div>
        <h3>tresemme</h3>
        <p>$18.99</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="vaseline" data-name="vaseline" data-price="30.00"
        data-image="./assets/resist/vaseline.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/vaseline.avif" alt="Bare Anatomy">
        </div>
        <h3>vaseline</h3>
        <p>$30.00</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
      <div class="deal" data-id="wanderlust" data-name="wanderlust" data-price="30.00"
        data-image="./assets/resist/wanderlust.avif">
        <div class="img-wrapper">
          <button class="like-btn"><i class="fa-solid fa-heart like-icon"></i></button>
          <img src="./assets/resist/wanderlust.avif" alt="Bare Anatomy">
        </div>
        <h3>wanderlust</h3>
        <p>$26.70</p>
        <button class="add-to-cart">Add to Cart</button>
      </div>
    </div>
  </section>

  <div class="trademark">
    <h1 class="heading5">Show Us <span>Some</span> Love</h1>

    <div class="box4">
      <div class="row5">
        <div class="icon"><img src="./assets/trademark/facebook_icon.avif" alt="facebook_icon"></div>
        <div class="icon"><img src="./assets/trademark/instagram_icon.avif" alt="instagram_icon"></div>
        <div class="icon"><img src="./assets/trademark/youtube_icon.avif" alt="youtube_icon"></div>
        <div class="icon"><img src="./assets/trademark/twitter_icon.avif" alt="twitter_icon"></div>
      </div> <br><br>
      <div class="row6">
        <div class="fullimg"><img src="./assets/trademark/return_icon.avif" alt=""></div>
      </div>
    </div>

  </div>

  <!-- searchbar script -->
  <script>
    function toggleSearchBar() {
      var overlay = document.getElementById("searchOverlay");
      var searchBox = document.getElementById("searchBox");
      var input = document.getElementById("searchInput");

      if (overlay.style.display === "flex") {
        searchBox.classList.remove("active");
        setTimeout(() => {
          overlay.style.display = "none";
          input.value = ""; // Clear search input
          filterProducts(); // Reset all product visibility
        }, 300);
      } else {
        overlay.style.display = "flex";
        setTimeout(() => {
          searchBox.classList.add("active");
          input.focus(); // Auto-focus input when opening
        }, 50);
      }
    }
  </script>

  <!-- slid no.1 -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const slider = document.querySelector(".slid");
      const images = Array.from(slider.children);
      const imageWidth = images[0].clientWidth; // Get width of the first image
      let autoSlideInterval;
      let isMouseDown = false;
      let startX;
      let scrollLeft;

      // Duplicate images to create infinite scroll effect
      images.forEach((img) => {
        let clone = img.cloneNode(true);
        slider.appendChild(clone);
      });

      // Auto Scroll Function (Scroll one image every 2 seconds)
      function autoSlide() {
        autoSlideInterval = setInterval(() => {
          slider.scrollBy({
            left: imageWidth,
            behavior: "smooth"
          });

          // If the slider reaches the middle of the duplicated set, reset scroll position
          if (slider.scrollLeft >= slider.scrollWidth / 2) {
            slider.scrollTo({
              left: 0,
              behavior: "smooth"
            });
          }
        }, 2000); // Adjust interval if needed
      }

      autoSlide();

      // Mouse Down (Desktop)
      slider.addEventListener("mousedown", (e) => {
        isMouseDown = true;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
      });

      // Mouse Move (Desktop)
      slider.addEventListener("mousemove", (e) => {
        if (!isMouseDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const scroll = (x - startX) * 2; // Multiply by 2 for faster movement
        slider.scrollLeft = scrollLeft - scroll;
      });

      // Mouse Up (Desktop)
      slider.addEventListener("mouseup", () => {
        isMouseDown = false;
      });

      // Touch Start (Mobile)
      slider.addEventListener("touchstart", (e) => {
        isMouseDown = true;
        startX = e.touches[0].pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
      });

      // Touch Move (Mobile)
      slider.addEventListener("touchmove", (e) => {
        if (!isMouseDown) return;
        e.preventDefault();
        const x = e.touches[0].pageX - slider.offsetLeft;
        const scroll = (x - startX) * 2; // Multiply by 2 for faster movement
        slider.scrollLeft = scrollLeft - scroll;
      });

      // Touch End (Mobile)
      slider.addEventListener("touchend", () => {
        isMouseDown = false;
      });
    });
  </script>

</body>

</html>