<?php
/**
 * API endpoint that marks a story as viewed and returns its data.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$storyId = intval($_POST['story_id'] ?? 0);

$stmt = $conn->prepare("SELECT user_id FROM stories WHERE id = ?");
$stmt->bind_param("i", $storyId);
$stmt->execute();
$story = $stmt->get_result()->fetch_assoc();

if ($story && $story['user_id'] == $_SESSION['user_id']) {
    $result = $conn->query("SELECT views FROM stories WHERE id = $storyId");
    $views = $result->fetch_assoc()['views'] ?? 0;
    echo json_encode(['success' => true, 'views' => $views, 'skipped' => true]);
    exit;
}

$stmt = $conn->prepare("INSERT IGNORE INTO story_views (story_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $storyId, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $conn->query("UPDATE stories SET views = views + 1 WHERE id = $storyId");
}

$result = $conn->query("SELECT views FROM stories WHERE id = $storyId");
$views = $result->fetch_assoc()['views'] ?? 0;

echo json_encode(['success' => true, 'views' => $views]);
exit;
?>