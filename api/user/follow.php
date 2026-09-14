<?php
/**
 * API endpoint that follows, unfollows, or requests access to another user.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$followingId = intval($_POST['following_id'] ?? 0);
$followerId = $_SESSION['user_id'];

if ($followerId === $followingId) {
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself.']);
    exit;
}

$stmt = $conn->prepare("SELECT is_private, username FROM users WHERE id = ?");
$stmt->bind_param("i", $followingId);
$stmt->execute();
$targetUser = $stmt->get_result()->fetch_assoc();
if (!$targetUser) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $followerId, $followingId);
$stmt->execute();
$stmt->store_result();
$isFollowing = $stmt->num_rows > 0;
$stmt->close();

if ($isFollowing) {
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $followerId, $followingId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM follow_requests WHERE requester_id = ? AND requested_id = ? AND status = 'accepted'");
    $stmt->bind_param("ii", $followerId, $followingId);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE following_id = ?");
    $stmt->bind_param("i", $followingId);
    $stmt->execute();
    $theirFollowerCount = $stmt->get_result()->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE follower_id = ?");
    $stmt->bind_param("i", $followerId);
    $stmt->execute();
    $myFollowingCount = $stmt->get_result()->fetch_assoc()['total'];

    echo json_encode(['success' => true, 'following' => false, 'followerCount' => $theirFollowerCount, 'myFollowingCount' => $myFollowingCount]);
    exit;
}

if ($targetUser['is_private']) {
    $stmt = $conn->prepare("SELECT id FROM follow_requests WHERE requester_id = ? AND requested_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $followerId, $followingId);
    $stmt->execute();
    $stmt->store_result();
    $existing = $stmt->num_rows;
    $stmt->close();

    if ($existing > 0) {
        echo json_encode(['success' => false, 'message' => 'Follow request already sent.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO follow_requests (requester_id, requested_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $followerId, $followingId);
    $stmt->execute();

    $msg = $_SESSION['username'] . ' has requested to follow you.';
    $link = '/CosmicDiary/pages/explorer.php?id=' . $followerId;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'follow_request', ?, ?)");
    $stmt->bind_param("iss", $followingId, $msg, $link);
    $stmt->execute();

    echo json_encode(['success' => true, 'following' => false, 'requested' => true]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
$stmt->bind_param("ii", $followerId, $followingId);
$stmt->execute();

$msg = $_SESSION['username'] . ' started following you.';
$link = '/CosmicDiary/pages/explorer.php?id=' . $followerId;
$stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'follow', ?, ?)");
$stmt->bind_param("iss", $followingId, $msg, $link);
$stmt->execute();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE following_id = ?");
$stmt->bind_param("i", $followingId);
$stmt->execute();
$theirFollowerCount = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $followerId);
$stmt->execute();
$myFollowingCount = $stmt->get_result()->fetch_assoc()['total'];

echo json_encode(['success' => true, 'following' => true, 'followerCount' => $theirFollowerCount, 'myFollowingCount' => $myFollowingCount]);
exit;
?>