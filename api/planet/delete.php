<?php
/**
 * API endpoint that deletes a planet.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $checkStmt = $conn->prepare("SELECT user_id FROM planets WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $planet = $checkStmt->get_result()->fetch_assoc();

    if (!$planet || $planet['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM planets WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Planet destroyed.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete planet.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
?>