<?php
/**
 * API endpoint that updates the current user's account details.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$bio = $conn->real_escape_string($_POST['bio'] ?? '');
$isPrivate = isset($_POST['is_private']) ? 1 : 0;
$avatar = null;

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $targetDir = "../../uploads/avatars/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $avatar = time() . '_' . basename($_FILES['avatar']['name']);
    move_uploaded_file($_FILES['avatar']['tmp_name'], $targetDir . $avatar);
    $stmt = $conn->prepare("UPDATE users SET bio = ?, is_private = ?, avatar = ? WHERE id = ?");
    $stmt->bind_param("sisi", $bio, $isPrivate, $avatar, $_SESSION['user_id']);
} else {
    $stmt = $conn->prepare("UPDATE users SET bio = ?, is_private = ? WHERE id = ?");
    $stmt->bind_param("sii", $bio, $isPrivate, $_SESSION['user_id']);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
}
exit;
?>