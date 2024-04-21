<?php
session_start();
header('Content-Type: application/json');

$config = parse_ini_file('db.ini');
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$search_term = $conn->real_escape_string($_POST['search_term']);

// 修改查询以联合 product_images 表
$query = "SELECT p.*, pi.image_path FROM products p 
          LEFT JOIN product_images pi ON p.id = pi.product_id 
          WHERE p.name LIKE '%$search_term%' OR p.description LIKE '%$search_term%'";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $_SESSION['products'] = $products; // 存储搜索结果到 session
    header('Location: ../shop-grid-search.html'); // 重定向到结果页面
} else {
    $_SESSION['no_results'] = 'No products found.'; // 存储没有找到商品的消息
    header('Location: ../shop-grid-search.html'); // 重定向到结果页面
}

$conn->close();
?>
