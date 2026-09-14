<?php
/**
 * Followers and following management view for an explorer profile.
 */
$pageTitle = 'Connections';
require_once '../includes/header.php';

$explorerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$activeTab = $_GET['tab'] ?? 'followers';
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $explorerId;
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:600px;width:100%;">
       <a href="<?= isset($_SESSION['user_id']) && $_SESSION['user_id'] == $explorerId ? 'dashboard.php' : 'explorer.php?id=' . $explorerId ?>" style="color:#00f7ff;text-decoration:none;font-family:'Orbitron',sans-serif;display:block;margin-bottom:1rem;">&larr; <?= isset($_SESSION['user_id']) && $_SESSION['user_id'] == $explorerId ? 'Back to Dashboard' : 'Back to Profile' ?></a>
        <div style="display:flex;gap:1rem;margin-bottom:1.5rem;">
            <button onclick="switchTab('followers')" id="tab-followers" class="tab-btn" style="flex:1;padding:0.6rem;background:<?= $activeTab === 'followers' ? 'var(--purple)' : 'rgba(255,255,255,0.05)' ?>;color:white;border:1px solid rgba(255,255,255,0.1);border-radius:8px;cursor:pointer;font-family:'Orbitron',sans-serif;">Followers</button>
            <button onclick="switchTab('following')" id="tab-following" class="tab-btn" style="flex:1;padding:0.6rem;background:<?= $activeTab === 'following' ? 'var(--purple)' : 'rgba(255,255,255,0.05)' ?>;color:white;border:1px solid rgba(255,255,255,0.1);border-radius:8px;cursor:pointer;font-family:'Orbitron',sans-serif;">Following</button>
        </div>
        
        <div id="connectionsList"></div>
    </div>
</div>

<script>
const explorerId = <?= $explorerId ?>;
let currentTab = '<?= $activeTab ?>';
const isOwner = <?= $isOwner ? 'true' : 'false' ?>;

/**
 * Switches the active connections tab and reloads its list.
 */
function switchTab(tab) {
    if (currentTab === tab) return;
    currentTab = tab;
    document.querySelectorAll('.tab-btn').forEach(btn => btn.style.background = 'rgba(255,255,255,0.05)');
    document.getElementById('tab-' + tab).style.background = 'var(--purple)';
    loadConnections();
}

/**
 * Loads followers or following users for the current explorer.
 */
function loadConnections() {
    const container = document.getElementById('connectionsList');
    container.style.opacity = '0.5';
    
    fetch(`/CosmicDiary/api/user/follow-list.php?id=${explorerId}&type=${currentTab}`)
        .then(res => res.json())
        .then(users => {
            if (users.length === 0) {
                container.innerHTML = `<p>No ${currentTab} yet.</p>`;
                container.style.opacity = '1';
                return;
            }
            container.innerHTML = users.map(u => `
                <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem;border-bottom:1px solid rgba(255,255,255,0.05);cursor:pointer;justify-content:space-between;flex-wrap:nowrap;" onclick="window.location.href='explorer.php?id=${u.id}'">
                    <div style="display:flex;align-items:center;gap:1rem;min-width:0;flex-shrink:1;">
                        <img src="${u.avatar_url}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:1px solid rgba(255,255,255,0.2);flex-shrink:0;">
                        <div style="min-width:0;">
                            <span style="color:#00f7ff;font-family:'Orbitron',sans-serif;white-space:nowrap;">${u.username}</span>
                            ${u.is_online ? '<span style="display:block;font-size:0.7rem;color:#00ff88;white-space:nowrap;">Online</span>' : '<span style="display:block;font-size:0.7rem;color:#b0b0ff;white-space:nowrap;">Offline</span>'}
                        </div>
                    </div>
                    ${isOwner ? `
                        <button onclick="event.stopPropagation(); removeConnection(${u.id}, '${currentTab}')" class="btn-delete" style="width:auto;padding:0.3rem 0.7rem;font-size:0.7rem;flex-shrink:0;"><i class="fas fa-user-minus"></i> Remove</button>
                    ` : ''}
                </div>
            `).join('');
            container.style.opacity = '1';
        });
}

/**
 * Removes a follower or following relationship from the profile view.
 */
function removeConnection(userId, type) {
    if (!confirm('Remove this ' + (type === 'followers' ? 'follower' : 'following') + '?')) return;
    
    const formData = new FormData();
    
    if (type === 'following') {
        formData.append('following_id', userId);
        fetch('/CosmicDiary/api/user/follow.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Unfollowed!', 'success');
                    loadConnections();
                } else {
                    showToast(data.message, 'error');
                }
            });
    } else {
        formData.append('follower_id', userId);
        fetch('/CosmicDiary/api/user/remove-follower.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Follower removed!', 'success');
                    loadConnections();
                } else {
                    showToast(data.message, 'error');
                }
            });
    }
}

loadConnections();
</script>
<?php require_once '../includes/footer.php'; ?>