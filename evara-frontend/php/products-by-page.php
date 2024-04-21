<?php
session_start();
$config = parse_ini_file('db.ini');
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$perPage = 9; // 每页显示的商品数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 当前页码
$offset = ($page - 1) * $perPage; // 计算分页偏移量

// 查询当前页的商品数据
$query = "SELECT * FROM products LIMIT $offset, $perPage";
$result = $conn->query($query);
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// 查询总商品数以计算总页数
$totalQuery = "SELECT COUNT(*) AS total FROM products";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $perPage);

$conn->close();

echo json_encode(['products' => $products, 'totalPages' => $totalPages]);
?>
