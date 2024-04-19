<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    // 用户未登录，重定向到登录页面
    header('Location: ../page-login-register.html');
    exit;
}

// 用户已登录，可以使用$_SESSION['user_id']获取用户ID
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

// 获取通过 URL 传递的产品 ID，默认为 1
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 1;

$sql = "SELECT name, description, price FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$product = [];
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    $product = array('error' => 'No product found with the given ID.');
}

$stmt->close();
$conn->close();

echo json_encode($product);
?>
