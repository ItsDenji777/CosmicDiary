<?php
/**
 * Shared helper functions used across pages and API endpoints.
 */
require_once 'config.php';

/**
 * Checks whether a user session is active.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirects the browser to a page inside the app.
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Trims and escapes user input before it is used in SQL.
 */
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Fetches a user record by ID for profile and permission checks.
 */
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, email, avatar, bio, is_private FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>