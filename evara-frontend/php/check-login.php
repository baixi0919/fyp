<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    // 用户未登录
    echo json_encode(array('loggedIn' => false));
} else {
    // 用户已登录
    echo json_encode(array('loggedIn' => true, 'user_id' => $_SESSION['user_id']));
}
?>
