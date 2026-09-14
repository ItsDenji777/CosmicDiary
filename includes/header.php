<?php
/**
 * Shared header, navigation, and page shell setup.
 */
require_once 'config.php';
require_once 'functions.php';
if (isLoggedIn()) {
    $stmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmic Diary - <?= $pageTitle ?? 'Home' ?></title>
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('<?= BASE_URL ?>assets/fonts/Orbitron[wght].ttf') format('truetype');
            font-weight: 400 700;
            font-display: swap;
        }
        @font-face {
            font-family: 'Space Mono';
            src: url('<?= BASE_URL ?>assets/fonts/SpaceMono-Regular.woff2') format('truetype');
            font-weight: 400;
            font-display: swap;
        }
        @font-face {
            font-family: 'Space Mono';
            src: url('<?= BASE_URL ?>assets/fonts/SpaceMono-Bold.woff2') format('truetype');
            font-weight: 700;
            font-display: swap;
        }
    </style>
    <link rel="stylesheet" href="<?= BASE_URL ?>node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>assets/images/favicon.svg">
    <link rel="preload" href="<?= BASE_URL ?>assets/images/logo.svg" as="image">
    <script src="<?= BASE_URL ?>node_modules/gif.js/dist/gif.js"></script>
</head>
<body>
<div class="warp-overlay" id="warpOverlay"></div>
<canvas id="starfield"></canvas>
<nav class="glass-nav">
    <a href="<?= BASE_URL ?>pages/home.php" class="logo">
        <img src="<?= BASE_URL ?>assets/images/logo.svg" alt="Cosmic Diary" class="logo-icon">
        Cosmic Diary
    </a>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="about.php">Mission Control</a></li>
        <li><a href="captain-log.php">Captain's Log</a></li>
        <li><a href="planet-builder.php">Planet Builder</a></li>
        <li><a href="planet-gallery.php">Galaxy Gallery</a></li>
        <?php if (isLoggedIn()): ?>
            <li><a href="dashboard.php"><i class="fas fa-user-astronaut"></i> <?= $_SESSION['username'] ?></a></li>
            <li><a href="profile.php"><i class="fas fa-cog"></i> Profile</a></li>
            <li style="position:relative;"><a href="notifications.php"><i class="fas fa-inbox"></i> Inbox</a><span id="notifBadge" style="position:absolute;top:-5px;right:-8px;background:#ff00c8;color:white;font-size:0.6rem;padding:2px 5px;border-radius:10px;display:none;font-family:'Space Mono',monospace;">0</span></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php" class="btn-register">Join Mission</a></li>
        <?php endif; ?>
    </ul>
</nav>
<main>