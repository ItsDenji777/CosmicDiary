<?php
/**
 * API endpoint that deletes a story highlight.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$storyId = intval($_POST['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM stories WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $storyId, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Story not found or not yours.']);
}
exit;
?>