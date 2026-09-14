<?php
/**
 * Application configuration and database connection bootstrap.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cosmic_diary');
date_default_timezone_set('Asia/Tehran');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$conn->query("SET time_zone = '+03:30'");

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/');
    session_start();
    header('X-Session-ID: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'none'));
}

define('BASE_URL', 'http://localhost/CosmicDiary/');
?>