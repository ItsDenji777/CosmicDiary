<?php
/**
 * Edit-post page that loads an existing log entry for editing.
 */
$pageTitle = 'Edit Log Entry';
require_once '../includes/session.php';
require_once '../includes/header.php';

$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:600px;width:100%;" id="editPostContainer">
        <div class="skeleton" style="height:400px;border-radius:16px;"></div>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/blog.js"></script>
<?php require_once '../includes/footer.php'; ?>