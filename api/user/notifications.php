<?php
/**
 * API endpoint that returns user notifications.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$action = $_GET['action'] ?? 'list';
$userId = $_SESSION['user_id'];

if ($action === 'count') {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    exit;
}

if ($action === 'clear') {
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'mark_read') {
    $notifId = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notifId, $userId);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->bind_param("i", $userId);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($notifications);
exit;
?>