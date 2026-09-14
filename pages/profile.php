<?php
/**
 * Own profile page for editing account details and privacy settings.
 */
$pageTitle = 'Profile Settings';
require_once '../includes/session.php';
require_once '../includes/header.php';
$user = getUserById($_SESSION['user_id']);
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:500px;width:100%;">
        <h2>Edit Profile</h2>
        <form id="profileForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4" style="width:100%;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;font-family:inherit;resize:vertical;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                    <input type="checkbox" id="isPrivate" name="is_private" <?= ($user['is_private'] ?? false) ? 'checked' : '' ?> style="width:auto;accent-color:var(--cyan);">
                    <span>Private Profile (only you can see your content)</span>
                </label>
            </div>
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>

        <hr style="border-color:rgba(255,255,255,0.1);margin:2rem 0;">

        <h2>Change Password</h2>
        <form id="passwordForm">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required minlength="6">
            </div>
            <button type="submit" class="btn-primary">Update Password</button>
        </form>
    </div>
</div>

<script>
// Submit profile changes through the API.
document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(document.getElementById('profileForm'));
    const res = await fetch('/CosmicDiary/api/user/update.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        showToast('Profile updated!', 'success');
    } else {
        showToast(data.message, 'error');
    }
});

// Submit a password change through the API.
document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        showToast('New passwords do not match.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('currentPassword', document.getElementById('currentPassword').value);
    formData.append('newPassword', newPassword);
    
    const res = await fetch('/CosmicDiary/api/user/change-password.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        showToast('Password changed successfully!', 'success');
        document.getElementById('passwordForm').reset();
    } else {
        showToast(data.message, 'error');
    }
});
</script>
<?php require_once '../includes/footer.php'; ?>