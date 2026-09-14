<?php
/**
 * Create-post page for publishing a new captain's log entry.
 */
$pageTitle = 'New Log Entry';
require_once '../includes/session.php';
require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="glass-card" style="max-width:600px;">
        <h2>New Log Entry</h2>
        <form id="createPostForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="body">Your Story</label>
                <textarea id="body" name="body" rows="10" required style="width:100%;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;font-family:inherit;resize:vertical;"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn-primary">Publish Log</button>
        </form>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/blog.js"></script>
<?php require_once '../includes/footer.php'; ?>