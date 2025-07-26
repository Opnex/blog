<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$dir = '../assets/post_images/';
if (!is_dir($dir)) mkdir($dir, 0777, true);
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed) && $file['size'] <= 2*1024*1024) {
        $filename = 'img_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $dest = $dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $url = '/opnex_blog/assets/post_images/' . $filename;
            echo json_encode(['location' => $url]);
            exit;
        }
    }
}
http_response_code(400);
echo json_encode(['error' => 'Upload failed']); 