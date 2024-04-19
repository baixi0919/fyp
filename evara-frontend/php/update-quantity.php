<?php
session_start();

// 检查用户是否已登录并获取用户ID
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // 读取数据库配置
    $config = parse_ini_file('db.ini');

    // 建立数据库连接
    $conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 确保请求方法为POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        
        // 更新购物车表
        $stmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE product_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $product_id, $user_id);
        
        if ($stmt->execute()) {
            echo "Quantity updated successfully";
        } else {
            echo "Error updating quantity: " . $stmt->error;
        }
    
        $stmt->close();
    }

    $conn->close();
} else {
    // 用户未登录，返回JSON格式的错误信息
    echo json_encode(array('success' => false, 'message' => '用户未登录'));
}
?>
