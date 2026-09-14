<?php
/**
 * API endpoint that toggles a planet like.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$planetId = intval($_POST['planet_id']);
$userId = $_SESSION['user_id'];

$checkStmt = $conn->prepare("SELECT id FROM planet_likes WHERE user_id = ? AND planet_id = ?");
$checkStmt->bind_param("ii", $userId, $planetId);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $deleteStmt = $conn->prepare("DELETE FROM planet_likes WHERE user_id = ? AND planet_id = ?");
    $deleteStmt->bind_param("ii", $userId, $planetId);
    $deleteStmt->execute();
    echo json_encode(['success' => true, 'liked' => false]);
} else {
    $insertStmt = $conn->prepare("INSERT INTO planet_likes (user_id, planet_id) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $userId, $planetId);
    $insertStmt->execute();
    echo json_encode(['success' => true, 'liked' => true]);
}
exit;
?>