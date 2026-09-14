<?php
/**
 * Single planet detail page.
 */
$pageTitle = 'Planet View';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:800px;width:100%;" id="singlePlanet"></div>
</div>
<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<?php require_once '../includes/footer.php'; ?>