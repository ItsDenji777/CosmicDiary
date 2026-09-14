<?php
/**
 * Login form page for returning users.
 */
$pageTitle = 'Login';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="auth-card glass-card">
        <h2>Captain's Login</h2>
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Launch</button>
        </form>
        <p class="auth-switch">New explorer? <a href="register.php">Join Mission</a></p>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/auth.js"></script>
<?php
require_once '../includes/footer.php';
?>