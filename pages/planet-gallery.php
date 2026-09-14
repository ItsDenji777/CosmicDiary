<?php
/**
 * Gallery page that lists community planets.
 */
$pageTitle = 'Galaxy Gallery';
require_once '../includes/header.php';
?>
<div class="blog-header">
    <h1><i class="fas fa-meteor"></i>Galaxy Gallery</h1>
    <?php if (isLoggedIn()): ?>
        <a href="planet-builder.php" class="btn-primary" style="width:auto;display:inline-block;padding:0.75rem 2rem;margin-right: 10px;margin-top: 10px;">Create Planet</a>
    <?php endif; ?>
</div>
<div class="blog-grid" id="planetGrid"></div>
<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<?php require_once '../includes/footer.php'; ?>