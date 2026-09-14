<?php
/**
 * API endpoint that removes a follower from the current user.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$followerId = intval($_POST['follower_id'] ?? 0);
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $followerId, $userId);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM follow_requests WHERE requester_id = ? AND requested_id = ? AND status = 'accepted'");
$stmt->bind_param("ii", $followerId, $userId);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Follower removed.']);
exit;
?>