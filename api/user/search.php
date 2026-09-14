<?php
/**
 * API endpoint that searches for users by name.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');
if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT username, avatar FROM users WHERE username LIKE ? LIMIT 5");
$like = '%' . $query . '%';
$stmt->bind_param("s", $like);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($users as &$user) {
    $user['avatar_url'] = $user['avatar'] ? '/CosmicDiary/uploads/avatars/' . $user['avatar'] : '/CosmicDiary/uploads/avatars/default.png';
}

echo json_encode($users);
exit;
?>