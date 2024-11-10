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
        echo "<div class='success'>Đăng ký camera thành công!</div>";
    } else {
        echo "<div class='error'>Lỗi khi thêm camera: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
