<?php
/**
 * API endpoint that loads published and editable log entries.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ? AND p.is_published = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    if ($post) {
        $post['is_owner'] = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']);
    }
    echo json_encode($post);
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
$offset = ($page - 1) * $limit;

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId > 0) {
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM posts WHERE is_published = 1 AND user_id = ?");
    $totalStmt->bind_param("i", $userId);
    $totalStmt->execute();
    $total = $totalStmt->get_result()->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id WHERE p.is_published = 1 AND p.user_id = ? ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("iii", $userId, $limit, $offset);
} else {
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM posts WHERE is_published = 1");
    $totalStmt->execute();
    $total = $totalStmt->get_result()->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id WHERE p.is_published = 1 ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($posts as &$post) {
    $post['is_owner'] = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']);
}

$totalPages = ceil($total / $limit);

echo json_encode(['posts' => $posts, 'totalPages' => $totalPages, 'currentPage' => $page]);
exit;
?>