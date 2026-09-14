<?php
/**
 * API endpoint that checks for new chat activity.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$ids = $_GET['ids'] ?? '';
if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$idsArray = array_map('intval', explode(',', $ids));
$idsList = implode(',', $idsArray);

$result = $conn->query("SELECT id, message, edited, deleted, created_at FROM chat_messages WHERE id IN ($idsList)");
$messages = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($messages);
exit;
?>