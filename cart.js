document.addEventListener("DOMContentLoaded", function () {
    displayCartItems();  // Load cart items when the page is ready
    setupCartIcon();
    setupAddToCartButtons();
    setupCloseCart();
    setupResistDealButtons(); // Set up event listeners for 'Hard to Resist Deals' section
});

// Setup cart icon click event (no cart count update)
function setupCartIcon() {
    let icon = document.getElementById("cart-icon");
    if (icon) {
        icon.addEventListener("click", function (event) {
            event.preventDefault();
            document.getElementById("cart-panel").classList.add("active");
            displayCartItems(); // Display items when the cart icon is clicked
        });
    }
}

// Setup close button
function setupCloseCart() {
    let closeBtn = document.getElementById("close-cart");
    if (closeBtn) {
        closeBtn.addEventListener("click", function () {
            document.getElementById("cart-panel").classList.remove("active");
        });
    }
}

// Attach event listener for "Add to Cart" buttons

function setupAddToCartButtons() {
    document.querySelectorAll(".add-to-cart").forEach(button => {
      if (!button.dataset.bound) {
        button.addEventListener("click", function (event) {
          event.preventDefault();
          const product = this.closest(".product");
          const id = product.dataset.id;
          const name = product.dataset.name;
          const price = parseFloat(product.dataset.price);
          const image = product.dataset.image;
  
          if (!id || !name || !price || !image) {
            alert("Error: Product details missing");
            return;
          }
  
          addToCart(id, name, price, image);
        });
  
        // ✅ Mark this button as already bound
        button.dataset.bound = "true";
      }
    });
  }
  

// Add product to cart
// Add product to cart
function addToCart(id, name, price, image) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingProduct = cart.find(item => item.id == id);

    if (existingProduct) {
        // If product already exists in cart, just increase quantity
        existingProduct.quantity++;
    } else {
        // If product doesn't exist in cart, add it
        cart.push({ id, name, price, image, quantity: 1 });
    }

    // Save updated cart to localStorage
    localStorage.setItem("cart", JSON.stringify(cart));

    alert(`${name} added to cart!`);
    displayCartItems();
}

// Display cart items
function displayCartItems() {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let cartContainer = document.getElementById("cart-items");
    let cartTotalElement = document.getElementById("cart-total");

    if (!cartContainer) return;

    cartContainer.innerHTML = "";

    if (cart.length === 0) {
        cartContainer.innerHTML = "<p>Your cart is empty!</p>";
        cartTotalElement.innerText = "Total: $0.00";
        return;
    }

    let total = 0;
    cart.forEach((item, index) => {
        total += item.price * item.quantity;

        let itemElement = document.createElement("div");
        itemElement.classList.add("cart-item");
        itemElement.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="cart-image">
            <div class="cart-info">
                <h3>${item.name}</h3>
                <p>Price: $${item.price.toFixed(2)}</p>
                <p>Quantity: 
                    <button onclick="changeQuantity(${index}, -1)">-</button>
                    ${item.quantity}
                    <button onclick="changeQuantity(${index}, 1)">+</button>
                </p>
            </div>
        `;
        cartContainer.appendChild(itemElement);
    });

    cartTotalElement.innerText = `Total: $${total.toFixed(2)}`;
}

// Change quantity
function changeQuantity(index, change) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    if (cart[index]) {
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    displayCartItems();
}

function sendCartToCheckout() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Create a form
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "checkout.php";

    // Create a hidden input field to store cart data
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cart_data";
    input.value = JSON.stringify(cart);

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();

    // Clear cart from localStorage after the order is placed
    localStorage.removeItem("cart");

    // Optionally, refresh the cart display (you can navigate to the order confirmation page if required)
    displayCartItems();  // This will show the cart is empty now
}


// Checkout
function checkout() {
    window.location.href = 'checkout.php';
}

// Setup event listeners for the 'Hard to Resist Deals' section
function setupResistDealButtons() {
    document.querySelectorAll('.deal').forEach(deal => {
        let addToCartButton = deal.querySelector('.add-to-cart');
        addToCartButton.addEventListener('click', function(event) {
            event.preventDefault();
            let id = deal.getAttribute('data-id');
            let name = deal.getAttribute('data-name');
            let price = parseFloat(deal.getAttribute('data-price'));
            let image = deal.getAttribute('data-image');

            if (!id || !name || !price || !image) {
                alert("Error: Product details are missing!");
                return;
            }

            addToCart(id, name, price, image);
        });
    });
}


