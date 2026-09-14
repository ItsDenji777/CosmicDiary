<?php
/**
 * API endpoint that loads active stories.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$conn->query("DELETE FROM stories WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");

$stmt = $conn->prepare("SELECT s.*, u.username, u.avatar FROM stories s JOIN users u ON s.user_id = u.id WHERE s.created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE) ORDER BY s.created_at ASC");
$stmt->execute();
$stories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($stories as &$story) {
    $story['avatar_url'] = $story['avatar'] ? '/CosmicDiary/uploads/avatars/' . $story['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
    if ($story['type'] === 'image') {
        $story['image_url'] = '/CosmicDiary/uploads/stories/' . $story['content'];
    }
    $story['is_mine'] = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $story['user_id'];
}

echo json_encode($stories);
exit;
?>