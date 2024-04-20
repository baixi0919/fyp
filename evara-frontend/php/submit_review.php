<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // 如果用户未登录，则重定向至登录页面
    header('Location: page-login-register.html');
    exit;
}

$config = parse_ini_file('db.ini');
$conn = new mysqli("localhost", $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $comment = $_POST['comment'] ?? '';

    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("INSERT INTO reviews (name, email, website, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $website, $comment);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // 重定向到产品详情页，并带有成功消息的参数
        header("Location: ../shop-product-full.html?review=success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
