<?php

session_start();

header('Content-Type: application/json');

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    // 用户未登录，返回错误信息
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// 读取数据库配置
$config = parse_ini_file('db.ini');

// 建立数据库连接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 检查数据库连接
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT products.id as product_id, products.name, products.description, products.price, carts.quantity FROM products INNER JOIN carts ON products.id = carts.product_id WHERE carts.user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => "Error preparing statement: " . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = floatval($row['price']); // Ensure price is a float
        $data[] = $row;
    }
}
echo json_encode($data); // 输出数据数组或空数组

$stmt->close();
$conn->close();
?>
