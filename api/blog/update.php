<?php
/**
 * API endpoint that updates an existing log entry.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = sanitize($_POST['title']);
    $body = sanitize($_POST['body']);

    $checkStmt = $conn->prepare("SELECT user_id, image FROM posts WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $post = $checkStmt->get_result()->fetch_assoc();

    if ($post['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $image = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $image);
    }

    $stmt = $conn->prepare("UPDATE posts SET title = ?, body = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $body, $image, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post updated.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update post.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>