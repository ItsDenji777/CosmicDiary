<?php
/**
 * API endpoint that returns a public profile view with access flags.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, username, email, avatar, bio, is_private, last_seen, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$user['is_private'] = (bool)($user['is_private'] ?? false);
$user['is_owner'] = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $userId;

$isFollowing = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $userId) {
    $followCheck = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $followCheck->bind_param("ii", $_SESSION['user_id'], $userId);
    $followCheck->execute();
    $followCheck->store_result();
    $isFollowing = $followCheck->num_rows > 0;
    $followCheck->close();
}

if ($user['is_private'] && !$user['is_owner'] && !$isFollowing) {
    echo json_encode([
        'id' => $user['id'],
        'username' => $user['username'],
        'avatar_url' => $user['avatar'] ? '/CosmicDiary/uploads/avatars/' . $user['avatar'] : '/CosmicDiary/uploads/avatars/default.png',
        'is_private' => true,
        'is_owner' => false,
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user['following_count'] = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE following_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user['follower_count'] = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM posts WHERE user_id = ? AND is_published = 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user['post_count'] = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM planets WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user['planet_count'] = $stmt->get_result()->fetch_assoc()['total'];

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $userId) {
    $checkStmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $checkStmt->bind_param("ii", $_SESSION['user_id'], $userId);
    $checkStmt->execute();
    $checkStmt->store_result();
    $user['is_following'] = $checkStmt->num_rows > 0;
    $checkStmt->close();
} else {
    $user['is_following'] = false;
}

$user['pending_request'] = null;
if (!$user['is_owner'] && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM follow_requests WHERE requester_id = ? AND requested_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $userId, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($reqId);
        $stmt->fetch();
        $user['pending_request'] = ['id' => $reqId];
    }
    $stmt->close();
}

$user['avatar_url'] = $user['avatar'] ? '/CosmicDiary/uploads/avatars/' . $user['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
$user['created_at_formatted'] = $user['created_at'] ? date('F j, Y', strtotime($user['created_at'])) : 'Unknown';
$user['last_seen_formatted'] = 'Never';
$user['is_online'] = false;
if ($user['last_seen']) {
    $lastSeenTime = strtotime($user['last_seen']);
    if ($lastSeenTime) {
        $user['last_seen_formatted'] = date('F j, Y \a\t H:i', $lastSeenTime);
        $user['is_online'] = (time() - $lastSeenTime) < 60;
    }
}

echo json_encode($user);
exit;
?>