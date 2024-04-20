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

$sql = "SELECT name, comment, created_at FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
$conn->close();
?>
