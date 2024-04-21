<?php
session_start();

$config = parse_ini_file('db.ini');
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// 获取表单数据
$name = $_POST['name'];
$email = $_POST['email'];
$telephone = $_POST['telephone'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// 预处理和绑定
$stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, telephone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $telephone, $subject, $message);

// 执行
if ($stmt->execute()) {
    // 使用 JavaScript 弹出消息
    echo "<script>alert('Successfully sent message. We will reply as soon as possible. Thank you!');</script>";
    echo "<script>window.location.href='../page-contact.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
