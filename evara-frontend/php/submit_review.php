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
    $product_id = $_POST['product_id'] ?? '';  // 获取隐藏的产品 ID

    // echo "Debug - Trying to insert:";
    // echo "Product ID: " . $product_id . "<br>";
    // echo "Name: " . $name . "<br>";
    // echo "Email: " . $email . "<br>";
    // echo "Website: " . $website . "<br>";
    // echo "Comment: " . $comment . "<br>";
    // exit;

    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, name, email, website, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $product_id, $name, $email, $website, $comment);
   
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // 重定向到对应的产品详情页，并带有成功消息的参数
        header("Location: ../shop-product-{$product_id}.html?review=success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
