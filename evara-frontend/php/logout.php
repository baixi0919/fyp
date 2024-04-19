<?php
session_start();

// 销毁会话
session_destroy();

echo json_encode(array('loggedOut' => true));

// 重定向到登录页面
header('Location: ../page-login-register.html');
exit;
?>
