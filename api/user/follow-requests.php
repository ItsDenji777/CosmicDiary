<?php
/**
 * API endpoint that manages follow requests for private profiles.
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = intval($_POST['request_id'] ?? 0);
    $status = $_POST['status'] ?? 'accepted';

    $fromNotification = ($_POST['from_notification'] ?? '0') === '1';
    
    if ($fromNotification) {
        $stmt2 = $conn->prepare("SELECT link FROM notifications WHERE id = ? AND user_id = ?");
        $stmt2->bind_param("ii", $requestId, $userId);
        $stmt2->execute();
        $notif = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
        
        if ($notif && preg_match('/id=(\d+)/', $notif['link'], $matches)) {
            $requesterIdFromLink = intval($matches[1]);
            $stmt = $conn->prepare("SELECT id, requester_id FROM follow_requests WHERE requester_id = ? AND requested_id = ? AND status = 'pending' LIMIT 1");
            $stmt->bind_param("ii", $requesterIdFromLink, $userId);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found.']);
            exit;
        }
    } else {
        $stmt = $conn->prepare("SELECT fr.id, fr.requester_id FROM follow_requests fr WHERE fr.id = ? AND fr.requested_id = ? AND fr.status = 'pending'");
        $stmt->bind_param("ii", $requestId, $userId);
    }
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();

    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Request not found.']);
        exit;
    }

    $requesterId = $request['requester_id'];

    if ($status === 'accepted') {
        $stmt = $conn->prepare("UPDATE follow_requests SET status = 'accepted' WHERE id = ?");
        $stmt->bind_param("i", $request['id']);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE follower_id = follower_id");
        $stmt->bind_param("ii", $requesterId, $userId);
        $stmt->execute();

        $msg = $_SESSION['username'] . ' accepted your follow request.';
        $link = '/CosmicDiary/pages/explorer.php?id=' . $userId;
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'follow', ?, ?)");
        $stmt->bind_param("iss", $requesterId, $msg, $link);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'follow_request' AND link = ?");
        $deleteLink = '/CosmicDiary/pages/explorer.php?id=' . $requesterId;
        $stmt->bind_param("is", $userId, $deleteLink);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Request accepted.']);
    } else {
        $stmt = $conn->prepare("UPDATE follow_requests SET status = 'declined' WHERE id = ?");
        $stmt->bind_param("i", $request['id']);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'follow_request' AND link = ?");
        $deleteLink = '/CosmicDiary/pages/explorer.php?id=' . $requesterId;
        $stmt->bind_param("is", $userId, $deleteLink);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Request declined.']);
    }
    exit;
}

$stmt = $conn->prepare("SELECT fr.id, fr.requester_id, u.username, u.avatar, fr.created_at FROM follow_requests fr JOIN users u ON fr.requester_id = u.id WHERE fr.requested_id = ? AND fr.status = 'pending' ORDER BY fr.created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($requests as &$req) {
    $req['avatar_url'] = $req['avatar'] ? '/CosmicDiary/uploads/avatars/' . $req['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
}

echo json_encode($requests);
exit;
?>