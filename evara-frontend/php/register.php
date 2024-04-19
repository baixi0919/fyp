<?php
session_start(); // 开始或继续一个会话

// 读取数据库配置
$config = parse_ini_file('db.ini');

// 建立数据库连接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 获取并转义用户输入
$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// 将密码转换为哈希值
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 创建预处理语句
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed_password);

// 执行语句
if ($stmt->execute() === TRUE) {
    // 获取新注册用户的 ID
    $last_id = $conn->insert_id;

    // 设置 session
    $_SESSION['user_id'] = $last_id; // 假设您有一个 user_id 字段存储在 session 中

    // 注册成功，重定向到首页
    header('Location: ../index.html');
    exit(); // 确保之后的代码不会被执行
} else {
    echo "Error: " . $stmt->error;
}

// 关闭连接
$stmt->close();
$conn->close();
?>
