<?php
/**
 * Account registration form page.
 */
$pageTitle = 'Join Mission';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="auth-card glass-card">
        <h2>Join the Mission</h2>
        <form id="registerForm">
            <div class="form-group">
                <label for="username">Astronaut Name</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <button type="submit" class="btn-primary">Create Account</button>
        </form>
        <p class="auth-switch">Already registered? <a href="login.php">Login here</a></p>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/auth.js"></script>
<?php
require_once '../includes/footer.php';
?>