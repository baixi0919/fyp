<?php
session_start();

header('Content-Type: application/json');

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
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

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

$sql = "SELECT name, comment, created_at FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
$stmt->close();
$conn->close();
?>
