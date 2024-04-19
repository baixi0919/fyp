<?php

session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    // 用户未登录，重定向到登录页面
    header('Location: ../page-login-register.html');
    exit;
}

// 用户已登录，可以使用 $_SESSION['user_id'] 获取用户ID
$user_id = $_SESSION['user_id'];

header('Content-Type: application/json');

// 读取数据库配置
$config = parse_ini_file('db.ini');

// 建立数据库连接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 获取产品ID和数量
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// 检查购物车中是否已有该商品
$check_sql = "SELECT quantity FROM carts WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_row = $check_result->fetch_assoc()) {
    // 商品已存在，更新其数量
    $new_quantity = $check_row['quantity'] + $quantity;
    $update_sql = "UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    $update_stmt->execute();
} else {
    // 商品不存在，插入新条目
    $insert_sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
}

// 关闭预处理语句和连接
$check_stmt->close();
if (isset($update_stmt)) {
    $update_stmt->close();
}
if (isset($insert_stmt)) {
    $insert_stmt->close();
}
$conn->close();

echo json_encode(['success' => true, 'message' => 'Cart updated']);
?>
