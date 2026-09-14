<?php
/**
 * API endpoint that saves a generated planet.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = $conn->real_escape_string($data['name']);
$backstory = $conn->real_escape_string($data['backstory'] ?? '');
$size = intval($data['size']);
$water = intval($data['water']);
$atmosphere = intval($data['atmosphere']);
$colorLand = $conn->real_escape_string($data['colorLand']);
$colorWater = $conn->real_escape_string($data['colorWater']);
$colorRing = isset($data['colorRing']) ? $conn->real_escape_string($data['colorRing']) : '#c8c8dc';
$colorMoon = isset($data['colorMoon']) ? $conn->real_escape_string($data['colorMoon']) : '#cccccc';
$moons = intval($data['moons']);
$ring = intval($data['ring']);
$rotationSpeed = isset($data['rotationSpeed']) ? intval($data['rotationSpeed']) : 5;

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Planet name is required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO planets (user_id, name, backstory, size, water, atmosphere, color_land, color_water, color_ring, color_moon, moons, ring, rotation_speed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issiiisssiiii", $_SESSION['user_id'], $name, $backstory, $size, $water, $atmosphere, $colorLand, $colorWater, $colorRing, $colorMoon, $moons, $ring, $rotationSpeed);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'planet_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save planet.']);
}
exit;
?>