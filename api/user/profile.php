<?php
/**
 * API endpoint that returns the current user's profile data.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT count(*) as postCount FROM posts WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$postCount = $stmt->get_result()->fetch_assoc()['postCount'];

$stmt2 = $conn->prepare("SELECT id, username, email, avatar, bio, last_seen, created_at FROM users WHERE id = ?");
$stmt2->bind_param("i", $userId);
$stmt2->execute();
$user = $stmt2->get_result()->fetch_assoc();
$user['postCount'] = $postCount;
$user['created_at_formatted'] = date('F j, Y', strtotime($user['created_at']));
$user['avatar_url'] = $user['avatar'] ? '/CosmicDiary/uploads/avatars/' . $user['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
$user['is_online'] = $user['last_seen'] && (time() - strtotime($user['last_seen'])) < 60;
$user['last_seen_formatted'] = $user['last_seen'] ? date('F j, Y \a\t H:i', strtotime($user['last_seen'])) : 'Never';

$stmt3 = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE follower_id = ?");
$stmt3->bind_param("i", $userId);
$stmt3->execute();
$user['following_count'] = $stmt3->get_result()->fetch_assoc()['total'];

$stmt4 = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE following_id = ?");
$stmt4->bind_param("i", $userId);
$stmt4->execute();
$user['follower_count'] = $stmt4->get_result()->fetch_assoc()['total'];

echo json_encode($user);
exit;
?>