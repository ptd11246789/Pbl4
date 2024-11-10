<?php
// Thông tin kết nối MySQL
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "camera_db";      

// Tạo kết nối đến MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Bắt đầu phần nội dung HTML
echo "<html lang='en'><head><title>Upload Status</title>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; }
        h1 { color: #333; }
        p { color: #555; }
       </style>";
echo "</head><body>";
echo "<div class='container'>";
echo "<h1>Capture Image and Video from IP Camera</h1>";
echo "<form method='POST' action=''>"; 
echo "<button type='submit'>Capture Image and Video</button>";
echo "</form>";

// Thêm JavaScript để tự động quay lại trang chủ sau 5 giây
echo "<meta http-equiv='refresh' content='5;url=index.html'>";
echo "</div>";

// Kiểm tra nếu yêu cầu là phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = false; 

    // Thông tin RTSP URL của camera
    $rtsp_url = "rtsp://admin:@192.168.1.11:554/stream"; // Thay thế bằng URL thực

    // Xử lý cắt ảnh
    $imageFile = 'uploads/images/snapshot_' . time() . '.jpg';
    if (!is_dir('uploads/images')) {
        mkdir('uploads/images', 0755, true);
    }
    
    $imageCommand = "ffmpeg -i $rtsp_url -frames:v 1 -q:v 2 $imageFile 2>&1";
    shell_exec($imageCommand);

    if (file_exists($imageFile)) {
        $uploadedAt = date('Y-m-d H:i:s');
        $sql = "INSERT INTO images (file_name, file_path, uploaded_at) 
                VALUES ('" . basename($imageFile) . "', '$imageFile', '$uploadedAt')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Image captured and saved to database successfully.</p>";
            $success = true;
        } else {
            echo "<p>Error saving image info to database: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Error capturing image.</p>";
    }

    // Xử lý cắt video
    $videoDir = 'uploads/videos';
if (!is_dir($videoDir)) {
    mkdir($videoDir, 0755, true);
}

// Đặt tên tệp video
$videoFile = $videoDir . '/video_' . time() . '.mp4';

// Thời gian ghi video (tính bằng giây)
$recordDuration = 5; // Thay đổi giá trị này theo nhu cầu của bạn

// Lệnh để ghi video từ camera IP
$videoCommand = "ffmpeg -i $rtsp_url -t $recordDuration -c:v libx264 -preset fast -crf 23 -c:a aac -b:a 192k $videoFile 2>&1";
$output = shell_exec($videoCommand);

// Kiểm tra nếu video đã được ghi thành công
if (file_exists($videoFile)) {
    $uploadedAt = date('Y-m-d H:i:s');
    $sql = "INSERT INTO videos (file_name, file_path, uploaded_at) 
            VALUES ('" . basename($videoFile) . "', '$videoFile', '$uploadedAt')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Video captured and saved to database successfully.</p>";
    } else {
        echo "<p>Error saving video info to database: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Error capturing video: " . $output . "</p>";
}

    if ($success) {
        echo "<p>You will be redirected to the homepage in 5 seconds.</p>";
    } else {
        echo "<p>There was an issue with your upload. Please try again.</p>";
    }
}

$conn->close();

echo "</body></html>";
?>
