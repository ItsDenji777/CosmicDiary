<?php
/**
 * API endpoint that creates a new log entry.
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
    $title = sanitize($_POST['title']);
    $body = sanitize($_POST['body']);
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $image);
    }

    if (empty($title) || empty($body)) {
        echo json_encode(['success' => false, 'message' => 'Title and body are required.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, body, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user_id'], $title, $body, $image);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'post_id' => $stmt->insert_id, 'redirect' => 'captain-log.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create post.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>