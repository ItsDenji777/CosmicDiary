<?php
/**
 * Captain's Log listing page for blog-style posts.
 */
$pageTitle = 'Captain\'s Log';
require_once '../includes/header.php';
?>
<div class="blog-header">
    <h1><i class="far fa-clipboard"></i>Captain's Log</h1>
    <?php if (isLoggedIn()): ?>
        <a href="log-create.php" class="btn-primary" style="width:auto;display:inline-block;padding:0.75rem 2rem;margin-right: 10px;margin-top: 10px;">New Log Entry</a>
    <?php endif; ?>
</div>
<div class="blog-grid" id="blogGrid"></div>
<div class="pagination" id="pagination"></div>
<script src="<?= BASE_URL ?>assets/js/blog.js"></script>
<?php require_once '../includes/footer.php'; ?>