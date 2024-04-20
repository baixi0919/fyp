<?php
header('Content-Type: application/json');

// 读取数据库配置
$config = parse_ini_file('db.ini');

// 建立数据库连接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 检查数据库连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 查询所有产品及其图片
$sql = "SELECT p.id, p.name, p.description, p.price, pi.image_path FROM products p LEFT JOIN product_images pi ON p.id = pi.product_id";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    // 输出每行数据
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    // 输出 JSON 格式的数据
    echo json_encode(['success' => true, 'products' => $products]);
} else {
    echo json_encode(['success' => false, 'message' => 'No products found']);
}

$conn->close();
?>
