<?php
/**
 * API endpoint that creates a story highlight.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$type = $_POST['type'] ?? 'text';
$content = $_POST['content'] ?? '';
$color = $_POST['color'] ?? '#7b2fff';

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Content cannot be empty.']);
    exit;
}

if ($type === 'image' && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $targetDir = "../../uploads/stories/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $filename = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $filename);
    $content = $filename;
}

$stmt = $conn->prepare("INSERT INTO stories (user_id, type, content, color) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $_SESSION['user_id'], $type, $content, $color);
$stmt->execute();

echo json_encode(['success' => true, 'story_id' => $stmt->insert_id]);
exit;
?>