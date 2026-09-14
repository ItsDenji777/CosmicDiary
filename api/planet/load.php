<?php
/**
 * API endpoint that loads planet data for display or editing.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT p.*, u.username, (SELECT COUNT(*) FROM planet_likes WHERE planet_id = p.id) as like_count FROM planets p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $planet = $stmt->get_result()->fetch_assoc();
    if ($planet) {
        $planet['is_owner'] = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $planet['user_id']);
        $planet['liked'] = false;
        if (isset($_SESSION['user_id'])) {
            $likeCheck = $conn->prepare("SELECT id FROM planet_likes WHERE user_id = ? AND planet_id = ?");
            $likeCheck->bind_param("ii", $_SESSION['user_id'], $planet['id']);
            $likeCheck->execute();
            $planet['liked'] = $likeCheck->get_result()->num_rows > 0;
        }
        $planet['color_ring'] = $planet['color_ring'] ?? '#c8c8dc';
        $planet['color_moon'] = $planet['color_moon'] ?? '#cccccc';
    }
    echo json_encode($planet);
    exit;
}

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId > 0) {
    $stmt = $conn->prepare("SELECT p.*, u.username, (SELECT COUNT(*) FROM planet_likes WHERE planet_id = p.id) as like_count FROM planets p JOIN users u ON p.user_id = u.id WHERE p.user_id = ? ORDER BY p.created_at DESC");
    $stmt->bind_param("i", $userId);
} else {
    $stmt = $conn->prepare("SELECT p.*, u.username, (SELECT COUNT(*) FROM planet_likes WHERE planet_id = p.id) as like_count FROM planets p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
}

$stmt->execute();
$planets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($planets as &$planet) {
    $planet['is_owner'] = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $planet['user_id']);
    $planet['liked'] = false;
    if (isset($_SESSION['user_id'])) {
        $likeCheck = $conn->prepare("SELECT id FROM planet_likes WHERE user_id = ? AND planet_id = ?");
        $likeCheck->bind_param("ii", $_SESSION['user_id'], $planet['id']);
        $likeCheck->execute();
        $planet['liked'] = $likeCheck->get_result()->num_rows > 0;
    }
    $planet['color_ring'] = $planet['color_ring'] ?? '#c8c8dc';
    $planet['color_moon'] = $planet['color_moon'] ?? '#cccccc';
}

echo json_encode($planets);
exit;
?>