<?php
/**
 * Notification inbox and request management page.
 */
$pageTitle = 'Notifications';
require_once '../includes/session.php';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:600px;width:100%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Orbitron',sans-serif;color:#00f7ff;">Notifications</h2>
            <button onclick="clearNotifications()" class="btn-delete" style="width:auto;padding:0.4rem 1rem;"><i class="fas fa-check-double"></i> Clear All</button>
        </div>
        <div id="notificationsList"><div class="skeleton" style="height:200px;border-radius:8px;"></div></div>
    </div>
</div>

<script>
/**
 * Loads the current user's notification list.
 */
function loadNotifications() {
    fetch('/CosmicDiary/api/user/notifications.php')
        .then(res => res.json())
        .then(notifs => {
            const container = document.getElementById('notificationsList');
            if (notifs.length === 0) {
                container.innerHTML = '<p style="text-align:center;padding:2rem;">No notifications yet.</p>';
                return;
            }
            container.innerHTML = notifs.map(n => `
                <div style="padding:0.75rem;border-bottom:1px solid rgba(255,255,255,0.05);cursor:pointer;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;"
                     onclick="markNotifRead(${n.id}, '${n.link || ''}')">
                    <div style="flex:1;min-width:200px;">
                        <i class="fas fa-${n.type==='follow'?'user-check':n.type==='follow_request'?'user-clock':'heart'}" style="color:#00f7ff;margin-right:0.5rem;"></i>
                        ${n.message}
                        <span style="display:block;font-size:0.7rem;color:#b0b0ff;margin-top:0.2rem;">${new Date(n.created_at).toLocaleString()}</span>
                    </div>
                    ${n.is_read==0 ? '<span style="width:8px;height:8px;border-radius:50%;background:#ff00c8;display:inline-block;"></span>' : ''}
                </div>
            `).join('');
        });
}

/**
 * Marks a notification as read and then refreshes the list.
 */
function markNotifRead(notifId, link) {
    fetch('/CosmicDiary/api/user/notifications.php?action=mark_read&id=' + notifId)
        .then(res => res.json())
        .then(() => {
            loadNotifications();
        });
    if (link) {
        setTimeout(() => {
            window.location.href = link;
        }, 300);
    }
}

/**
 * Clears all notifications for the current user.
 */
function clearNotifications() {
    fetch('/CosmicDiary/api/user/notifications.php?action=clear')
        .then(res => res.json())
        .then(() => {
            showToast('Notifications cleared!', 'success');
            loadNotifications();
        });
}

/**
 * Processes a follow request directly from a notification.
 */
function handleRequest(notifId, status) {
    const formData = new FormData();
    formData.append('request_id', notifId);
    formData.append('status', status);
    formData.append('from_notification', '1');
    fetch('/CosmicDiary/api/user/follow-requests.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) { 
                showToast(data.message, 'success'); 
                loadNotifications();
            } else { 
                showToast(data.message, 'error'); 
            }
        });
}

loadNotifications();
</script>
<?php require_once '../includes/footer.php'; ?>