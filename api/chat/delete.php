<?php
/**
 * API endpoint that deletes a chat message.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$messageId = intval($_POST['id'] ?? 0);

$stmt = $conn->prepare("UPDATE chat_messages SET deleted = 1 WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $messageId, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Message not found or not yours.']);
}
exit;
?>