<?php
/**
 * API endpoint that returns chat history for a room or thread.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

$stmt = $conn->prepare("SELECT cm.*, u.username, u.avatar, u.id as user_id FROM chat_messages cm JOIN users u ON cm.user_id = u.id WHERE cm.id > ? AND cm.deleted = 0 ORDER BY cm.id ASC LIMIT ?");
$stmt->bind_param("ii", $lastId, $limit);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($messages as &$msg) {
    $msg['avatar_url'] = $msg['avatar'] ? '/CosmicDiary/uploads/avatars/' . $msg['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
    $msg['is_mine'] = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $msg['user_id'];
}

echo json_encode($messages);
exit;
?>