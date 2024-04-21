<?php
session_start();

// 设置每页显示的商品数量
$perPage = 9;

// 从请求中获取最小和最大价格
$minPrice = isset($_GET['minPrice']) ? floatval($_GET['minPrice']) : 0;
$maxPrice = isset($_GET['maxPrice']) ? floatval($_GET['maxPrice']) : PHP_INT_MAX;

if (isset($_SESSION['products'])) {
    // 过滤商品数据根据价格范围
    $filteredProducts = array_filter($_SESSION['products'], function($product) use ($minPrice, $maxPrice) {
        return $product['price'] >= $minPrice && $product['price'] <= $maxPrice;
    });

    $totalItems = count($filteredProducts); // 更新总商品数
    $totalPages = ceil($totalItems / $perPage); // 计算总页数

    // 获取当前页码，默认为1
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // 计算当前页码开始的商品索引
    $start = ($currentPage - 1) * $perPage;

    // 获取当前页的商品数据
    $currentProducts = array_slice($filteredProducts, $start, $perPage);

    echo json_encode([
        'success' => true,
        'products' => $currentProducts,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'currentPage' => $currentPage
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No products found within the specified price range.']);
}
?>
