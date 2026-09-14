<?php
/**
 * API endpoint that creates mention notifications for chat messages.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$username = trim($_POST['username'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($username) || empty($message)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    $notifMsg = $_SESSION['username'] . ' mentioned you in chat: "' . substr($message, 0, 60) . '"';
    $link = '/CosmicDiary/pages/home.php';
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'follow', ?, ?)");
    $stmt->bind_param("iss", $user['id'], $notifMsg, $link);
    $stmt->execute();
}

echo json_encode(['success' => true]);
exit;
?>