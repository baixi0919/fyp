<?php
session_start();

// 设置每页显示的商品数量
$perPage = 9;

if (isset($_SESSION['products'])) {
    $totalItems = count($_SESSION['products']); // 总商品数
    $totalPages = ceil($totalItems / $perPage); // 计算总页数

    // 获取当前页码，默认为1
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    // 计算当前页码开始的商品索引
    $start = ($currentPage - 1) * $perPage;

    // 获取当前页的商品数据
    $currentProducts = array_slice($_SESSION['products'], $start, $perPage);

    echo json_encode([
        'success' => true,
        'products' => $currentProducts,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'currentPage' => $currentPage
    ]);
} elseif (isset($_SESSION['no_results'])) {
    echo json_encode(['success' => false, 'message' => $_SESSION['no_results']]);
    unset($_SESSION['no_results']); // 清除 session 数据
} else {
    echo json_encode(['success' => false, 'message' => 'No data available.']);
}
?>
