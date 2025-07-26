<?php
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'] ?? null;
    $user_id = $_SESSION['user_id'];

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($ext, $allowed) && $file['size'] <= 2*1024*1024) {
            $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $dest = '../assets/profile_pics/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $profile_picture = $filename;
            }
        }
    }

    try {
        // Check if new username/email already exists (excluding current user)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $user_id]);
        
        if ($stmt->fetch()) {
            header("Location: profile.php?error=username_email_taken");
            exit();
        }

        // Build update query
        $fields = 'username = ?, email = ?, bio = ?';
        $params = [$username, $email, $bio, $user_id];
        if ($profile_picture) {
            $fields .= ', profile_picture = ?';
            array_splice($params, 3, 0, $profile_picture); // Insert profile_picture before user_id
        }
        $sql = "UPDATE users SET $fields WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Update session username if changed
        $_SESSION['username'] = $username;
        header("Location: profile.php?success=profile_updated");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>