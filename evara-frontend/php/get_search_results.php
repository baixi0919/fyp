<?php
session_start();

if (isset($_SESSION['products'])) {
    echo json_encode(['success' => true, 'products' => $_SESSION['products']]);
    unset($_SESSION['products']); // 清除 session 数据
} elseif (isset($_SESSION['no_results'])) {
    echo json_encode(['success' => false, 'message' => $_SESSION['no_results']]);
    unset($_SESSION['no_results']); // 清除 session 数据
} else {
    echo json_encode(['success' => false, 'message' => 'No data available.']);
}
?>
