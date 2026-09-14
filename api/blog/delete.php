<?php
/**
 * API endpoint that deletes a log entry.
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

    $checkStmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $post = $checkStmt->get_result()->fetch_assoc();

    if (!$post || $post['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>