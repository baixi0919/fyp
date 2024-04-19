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
    echo json_encode(['success' => false, 'message' => "连接失败: " . $conn->connect_error]);
    exit;
}

// 查询商品表和购物车表
$sql = "SELECT products.id as product_id, products.name, products.description, products.price, carts.quantity FROM products INNER JOIN carts ON products.id = carts.product_id WHERE carts.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 准备要返回的数据数组
$data = [];

// 检查结果是否有数据
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = floatval($row['price']); // 将 price 转换为浮点数
        $data[] = $row;
    }
    echo json_encode($data); // 输出数据数组
} else {
    echo json_encode([]); // 结果为空时返回空数组
}

// 关闭数据库连接
$conn->close();
?>
