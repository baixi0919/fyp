<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: page-login-register.html');
    exit;
}

$config = parse_ini_file('db.ini');

// 建立數據庫連接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

$user_id = $_SESSION['user_id']; // 从 session 获取用户 ID
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$cname = $_POST['cname'];
$billing_address = $_POST['billing_address'];
$billing_address2 = $_POST['billing_address2'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = $_POST['zipcode'];
$phone = $_POST['phone'];
$email = $_POST['email'];

// 创建订单 SQL 查询
$sql = "INSERT INTO orders (user_id, product_id, quantity, address, phone_number) VALUES (?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // 假设您已有 product_id 和 quantity
    $product_id = 1; // 示例产品 ID
    $quantity = 1;   // 示例数量

    $address = $billing_address . " " . $billing_address2 . ", " . $city . ", " . $state . ", " . $zipcode;
    $stmt->bind_param("iiiss", $user_id, $product_id, $quantity, $address, $phone);

    if ($stmt->execute()) {
        echo "Order placed successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
