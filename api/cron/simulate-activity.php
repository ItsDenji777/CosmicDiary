<?php
/**
 * Maintenance task that simulates platform activity for testing or demos.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';

// Get all users except the main admin (ID 4)
$users = $conn->query("SELECT id FROM users WHERE id != 4")->fetch_all(MYSQLI_ASSOC);

// Random percentage between 15% and 45%
$percentage = rand(15, 45) / 100;
$onlineCount = ceil(count($users) * $percentage);
shuffle($users);
$onlineUsers = array_slice($users, 0, $onlineCount);

// Set these users as online now
foreach ($onlineUsers as $user) {
    $conn->query("UPDATE users SET last_seen = NOW() WHERE id = {$user['id']}");
}

// Random percentage for recent (10-25%)
$recentPct = rand(10, 25) / 100;
$recentCount = ceil(count($users) * $recentPct);
shuffle($users);
$recentUsers = array_slice($users, 0, $recentCount);
foreach ($recentUsers as $user) {
    $minutesAgo = rand(1, 10);
    $conn->query("UPDATE users SET last_seen = DATE_SUB(NOW(), INTERVAL {$minutesAgo} MINUTE) WHERE id = {$user['id']}");
}

// Set the rest as offline (hours ago)
$remainingIds = [];
foreach ($users as $u) {
    $remainingIds[] = $u['id'];
}
if (!empty($remainingIds)) {
    $idsList = implode(',', $remainingIds);
    $conn->query("UPDATE users SET last_seen = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 48) + 1 HOUR) WHERE id IN ($idsList) AND last_seen IS NULL");
}

echo json_encode(['success' => true, 'online' => $onlineCount]);
exit;
?>