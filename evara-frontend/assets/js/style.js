document.addEventListener('DOMContentLoaded', function () {
    fetchCartData();
    isLoggedIn();
});

// 发起Ajax请求获取购物车数据
function fetchCartData() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'php/get-data.php', true);
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            console.log(xhr.responseText);
            var cartItems = JSON.parse(xhr.responseText);
            updateCart(cartItems);
        }
    };
    xhr.send();
}

// 使用获取的数据更新购物车
function updateCart(cartItems) {
    // Assuming cartItems is the direct array from the response
    var cartList = document.querySelector('.cart-dropdown-wrap ul');
    var cartCount = document.querySelector('.mini-cart-icon .pro-count'); // Get the cart counter element
    var total = 0;
    var uniqueProductCount = 0; // To store count of different products

    // Clear the current cart list
    cartList.innerHTML = '';

    // If cart is not empty, display each item
    if (cartItems.length > 0) {
        cartCount.style.display = 'block'; // Show the cart counter
        cartItems.forEach(function (item) {
            var itemTotal = item.price * item.quantity;
            total += itemTotal; // Update total price

            // Build the list item and append to the cart list
            var listItem = `
                <li>
                    <div class="shopping-cart-title">
                        <h4><a href="shop-product-${item.product_id}.html">${item.name}</a></h4>
                        <h4><span>${item.quantity} × </span>$${item.price.toFixed(2)}</h4>
                    </div>
                    <div class="shopping-cart-delete">
                        <a href="#" onclick="removeCartItem(${item.product_id}); return false;"><i class="fi-rs-cross-small"></i></a>
                    </div>
                </li>
            `;
            cartList.innerHTML += listItem;
        });

        uniqueProductCount = cartItems.length; // The number of different products
        cartCount.textContent = uniqueProductCount; // Update the cart counter
    } else {
        // If cart is empty, show a message
        cartList.innerHTML = '<li><div class="shopping-cart-title"><h4>Cart is empty</h4></div></li>';
        cartCount.style.display = 'none'; // Hide the cart counter
    }

    // Update total
    var totalElement = document.querySelector('.shopping-cart-total h4 span');
    if (totalElement) { // Ensure the element exists
        totalElement.textContent = `$${total.toFixed(2)}`;
    }
}

// 移除购物车项的函数
function removeCartItem(productId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/remove-product.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            console.log('Product removed:', xhr.responseText);
            // 成功删除后，重新获取购物车数据来更新列表
            fetchCartData();
        } else {
            console.error('Server responded with status:', xhr.status);
        }
    };
    xhr.onerror = function () {
        console.error('Network error occurred');
    };
    xhr.send(`product_id=${productId}`);
}

function isLoggedIn() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'php/check-login.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var result = JSON.parse(xhr.responseText);
            var cartCounts = document.querySelectorAll('.pro-count.blue'); // 获取所有的计数器元素
            if (result.loggedIn) {
                document.getElementById('logoutLink').style.display = 'block';
                document.getElementById('loginLink').style.display = 'none';
                cartCounts.forEach(function (cartCount) {
                    cartCount.style.display = 'block'; // 用户已登录，显示购物车计数器
                });
            } else {
                document.getElementById('logoutLink').style.display = 'none';
                document.getElementById('loginLink').style.display = 'block';
                cartCounts.forEach(function (cartCount) {
                    cartCount.style.display = 'none'; // 用户未登录，隐藏购物车计数器
                });
            }
        }
    };
    xhr.send();
}