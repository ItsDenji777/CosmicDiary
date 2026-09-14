<?php
/**
 * Logged-in user dashboard with profile, posts, and planets.
 */
$pageTitle = 'Dashboard';
require_once '../includes/session.php';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:800px;width:100%;">
        <img id="dashboardAvatar" src="<?= BASE_URL ?>uploads/avatars/default.png" alt="Avatar" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid #00f7ff;">
        <h2>Mission Dashboard</h2>
        <div id="userStats"></div>
        <h3 style="margin-top:2rem;">Your Log Entries</h3>
        <div id="userPosts"></div>
        <h3 style="margin-top:2rem;">Your Planets</h3>
        <div id="userPlanets" class="blog-grid"></div>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/blog.js"></script>
<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<?php require_once '../includes/footer.php'; ?>