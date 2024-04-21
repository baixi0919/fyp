<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: page-login-register.html');
    exit;
}

$config = parse_ini_file('db.ini');
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$fname = $_POST['fname'] ?? '';
$lname = $_POST['lname'] ?? '';
$address = $_POST['billing_address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$total = $_POST['total'] ?? 0;

$stmt = $conn->prepare("INSERT INTO orders (user_id, fname, lname, address, city, state, phone, email, total, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("isssssssd", $user_id, $fname, $lname, $address, $city, $state, $phone, $email, $total);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    // 获取购物车中的商品
    $query = "SELECT carts.product_id, carts.quantity, products.price FROM carts JOIN products ON carts.product_id = products.id WHERE carts.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $insert_order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, user_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $insert_order_item_stmt->bind_param("iiidi", $order_id, $user_id, $row['product_id'], $row['quantity'], $row['price']);
        $insert_order_item_stmt->execute();
        $insert_order_item_stmt->close();
    }

    // 清空购物车
    $stmt = $conn->prepare("DELETE FROM carts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    echo "<script>
            alert('Order placed successfully.');
            window.location.href = '../index.html';
          </script>";
} else {
    echo "Error placing order: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
