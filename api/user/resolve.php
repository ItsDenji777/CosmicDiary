<?php
/**
 * API endpoint that resolves a username or user lookup request.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

$username = trim($_GET['username'] ?? '');
if (empty($username)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user) {
    echo json_encode(['success' => true, 'id' => $user['id']]);
} else {
    echo json_encode(['success' => false]);
}
exit;
?>