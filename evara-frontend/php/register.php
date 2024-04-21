<?php
session_start(); // 开始或继续一个会话

// 读取数据库配置
$config = parse_ini_file('db.ini');

// 建立数据库连接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 检查连接是否成功
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// 获取并转义用户输入
$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// 检查邮箱是否已存在
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists!');</script>";
    echo "<script>window.location.href='../page-login-register.html';</script>";
    $stmt->close();
    $conn->close();
    exit();
}

// 将密码转换为哈希值
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 创建预处理语句来插入新用户
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed_password);

// 执行语句
if ($stmt->execute() === TRUE) {
    // 获取新注册用户的 ID
    $last_id = $conn->insert_id;

    // 设置 session
    $_SESSION['user_id'] = $last_id;

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
