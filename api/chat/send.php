<?php
/**
 * API endpoint that sends a new chat message.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$message = trim($_POST['message'] ?? '');
$replyTo = intval($_POST['reply_to'] ?? 0);
$replyUsername = trim($_POST['reply_username'] ?? '');
$replyText = trim($_POST['reply_text'] ?? '');

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, reply_to, reply_username, reply_text) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isiss", $_SESSION['user_id'], $message, $replyTo, $replyUsername, $replyText);
$stmt->execute();

echo json_encode(['success' => true]);
exit;
?>