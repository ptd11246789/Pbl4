<?php
// add_camera.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $ip_address = $_POST['ip_address'];
    $port = $_POST['port'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stream_name = $_POST['stream_name'];

    // Kết nối tới cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "camera_db");
    if ($conn->connect_error) {
        die("Kết nối không thành công: " . $conn->connect_error);
    }

    // Thực thi truy vấn chèn dữ liệu
    $stmt = $conn->prepare("INSERT INTO camera (name, ip_address, port, username, password, stream_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $name, $ip_address, $port, $username, $password, $stream_name);

    if ($stmt->execute()) {
        echo "Đăng ký camera thành công!";
    } else {
        echo "Lỗi khi thêm camera: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Form -->
<form method="POST" action="add_camera.php">
    <label>Tên Camera:</label><input type="text" name="name" required><br>
    <label>Địa chỉ IP:</label><input type="text" name="ip_address" required><br>
    <label>Port:</label><input type="number" name="port" required><br>
    <label>Tên người dùng:</label><input type="text" name="username"><br>
    <label>Mật khẩu:</label><input type="password" name="password"><br>
    <label>Stream Name:</label><input type="text" name="stream_name"><br>
    <button type="submit">Thêm Camera</button>
</form>
