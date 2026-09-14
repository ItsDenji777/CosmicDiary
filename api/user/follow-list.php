<?php
/**
 * API endpoint that returns followers or following lists.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = $_GET['type'] ?? 'followers';

if ($type === 'followers') {
    $stmt = $conn->prepare("SELECT u.id, u.username, u.avatar, u.last_seen FROM follows f JOIN users u ON f.follower_id = u.id WHERE f.following_id = ? ORDER BY f.created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT u.id, u.username, u.avatar, u.last_seen FROM follows f JOIN users u ON f.following_id = u.id WHERE f.follower_id = ? ORDER BY f.created_at DESC");
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($users as &$user) {
    $user['avatar_url'] = $user['avatar'] ? '/CosmicDiary/uploads/avatars/' . $user['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
    $user['is_online'] = $user['last_seen'] && (time() - strtotime($user['last_seen'])) < 300;
}

echo json_encode($users);
exit;
?>