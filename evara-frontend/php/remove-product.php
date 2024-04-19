<?php
session_start();

header('Content-Type: application/json'); 

// 检查用户是否已登录并获取用户ID
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // 读取数据库配置
    $config = parse_ini_file('db.ini');

    // 建立数据库连接
    $conn = mysqli_connect("localhost", $config['username'], $config['password'], $config['database']);

    // 检查连接
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => "连接失败: " . $conn->connect_error]));
    }

    // 确保请求方法为POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = $_POST['product_id'];

        // 删除购物车表中的商品
        $stmt = $conn->prepare("DELETE FROM carts WHERE product_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $product_id, $user_id);
    
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Product removed successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Error removing product: " . $stmt->error]);
        }
    
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => '用户未登录']);
}
?>
