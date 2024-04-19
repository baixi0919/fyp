<?php
// 讀取數據庫配置
$config = parse_ini_file('db.ini');

// 建立數據庫連接
$conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

// 獲取並轉義用戶輸入
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// 查詢數據庫中的用戶記錄
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

// 驗證用戶名和密碼
if ($result->num_rows > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
        // 登錄成功，設置session
        session_start();
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['loggedIn'] = true;
        // 根据用户类型重定向到不同的页面
        if ($user['username'] == 'admin') {
            header('Location: ../../evara-backend/index.html');
        } else {
            header('Location: ../index.html');
        }
        exit;
               
    } else {
        echo "密碼錯誤";
        header('Location: ../page-login-register.html');
        exit;
    }
} else {
    echo "Email不存在";
}

// 關閉連接
$conn->close();
?>
