<?php
/**
 * API endpoint that updates profile privacy settings.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$isPrivate = intval($_POST['is_private'] ?? 0);

$stmt = $conn->prepare("UPDATE users SET is_private = ? WHERE id = ?");
$stmt->bind_param("ii", $isPrivate, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Privacy updated.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update privacy.']);
}
exit;
?>