let blogPage = 1;
let blogLoading = false;
let blogHasMore = true;

// Loads paginated log entries into the blog grid.
function loadPosts(page = 1) {
    if (blogLoading || !blogHasMore) return;
    blogLoading = true;
    
    const limit = window.innerWidth <= 768 ? 5 : 10;
    fetch(`/CosmicDiary/api/blog/read.php?page=${page}&limit=${limit}`)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('blogGrid');
            if (!grid) return;
            
            if (page === 1) grid.innerHTML = '';
            
            if (data.posts.length === 0) {
                blogHasMore = false;
                if (page === 1) grid.innerHTML = '<p>No log entries yet.</p>';
                blogLoading = false;
                return;
            }
            
            data.posts.forEach(post => {
                const card = document.createElement('div');
                card.className = 'glass-card post-card';
                card.innerHTML = `
                    ${post.image ? `<img src="/CosmicDiary/uploads/${post.image}" alt="${post.title}" class="post-image">` : ''}
                    <h3><a href="log-view.php?id=${post.id}" style="color:#00f7ff;">${post.title}</a></h3>
                    <p class="post-meta">By <a href="explorer.php?id=${post.user_id}" style="color:#ff00c8;text-decoration:none;" onclick="event.stopPropagation();">${post.username}</a> | ${new Date(post.created_at).toLocaleDateString()}</p>
                    <p class="post-excerpt">${post.body.substring(0, 150)}...</p>
                `;
                grid.appendChild(card);
            });
            
            blogPage = page + 1;
            blogLoading = false;
            
            if (data.posts.length < limit) blogHasMore = false;
        });
}

// Lazy-load log entries as the user scrolls the blog feed.
if (document.getElementById('blogGrid')) {
    loadPosts();
    
    window.addEventListener('scroll', () => {
        const grid = document.getElementById('blogGrid');
        if (!grid || !blogHasMore || blogLoading) return;
        
        const lastCard = grid.lastElementChild;
        if (!lastCard) return;
        
        const rect = lastCard.getBoundingClientRect();
        if (rect.bottom <= window.innerHeight + 200) {
            loadPosts(blogPage);
        }
    });
}

// Handle the log creation form submission.
const createForm = document.getElementById('createPostForm');
if (createForm) {
    createForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(createForm);
        try {
            const res = await fetch('/CosmicDiary/api/blog/create.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                sessionStorage.setItem('celebrate', 'log');
                showToast('Log entry published!', 'success');
                setTimeout(() => window.location.href = '/CosmicDiary/pages/captain-log.php', 500);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('Connection error.', 'error');
        }
    });
}

const blogParams = new URLSearchParams(window.location.search);
const postId = blogParams.get('id');
// Render the single-post view when a log ID is present.
if (postId && document.getElementById('singlePost')) {
    fetch(`/CosmicDiary/api/blog/read.php?id=${postId}`)
        .then(res => res.json())
        .then(post => {
            if (!post) {
                document.getElementById('singlePost').innerHTML = '<p>Post not found.</p>';
                return;
            }
            document.getElementById('singlePost').innerHTML = `
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                    <a href="captain-log.php" style="color:#00f7ff;text-decoration:none;font-family:'Orbitron',sans-serif;">&larr; Back to Logs</a>
                    ${post.is_owner ? `
                        <div style="display:flex;gap:0.5rem;">
                            <button onclick="window.location.href='log-edit.php?id=${post.id}'" class="btn-edit"><i class="fas fa-pencil-alt"></i> Edit</button>
                            <button onclick="deletePost(${post.id})" class="btn-delete"><i class="fas fa-trash-alt"></i> Delete Log</button>
                        </div>
                    ` : ''}
                </div>
                ${post.image ? `<img src="/CosmicDiary/uploads/${post.image}" alt="${post.title}" style="width:100%;border-radius:12px;margin-bottom:1rem;">` : ''}
                <h1 style="font-family:'Orbitron',sans-serif;color:#00f7ff;">${post.title}</h1>
                <p style="color:#b0b0ff;">By <a href="explorer.php?id=${post.user_id}" style="color:#ff00c8;text-decoration:none;">${post.username}</a> | ${new Date(post.created_at).toLocaleDateString()}</p>
                <div style="margin-top:1.5rem;line-height:1.8;">${post.body}</div>
            `;
        });
}

// Deletes a log entry after confirmation.
function deletePost(id) {
    showConfirm('Delete Log', 'This log entry will be permanently deleted. This cannot be undone.', 'fa-trash-alt', () => {
        const formData = new FormData();
        formData.append('id', id);
        fetch('/CosmicDiary/api/blog/delete.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Log deleted.', 'success');
                    setTimeout(() => window.location.href = '/CosmicDiary/pages/captain-log.php', 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
    });
}

// Load the edit form, then submit updated log content.
if (document.getElementById('editPostContainer')) {
    const editPostId = new URLSearchParams(window.location.search).get('id');
    if (editPostId) {
        fetch(`/CosmicDiary/api/blog/read.php?id=${editPostId}`)
            .then(res => res.json())
            .then(post => {
                if (!post || !post.is_owner) {
                    document.getElementById('editPostContainer').innerHTML = '<p>Unauthorized or post not found.</p>';
                    return;
                }
                document.getElementById('editPostContainer').innerHTML = `
                    <h2>Edit Log Entry</h2>
                    <form id="editPostForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="${post.id}">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" value="${post.title}" required>
                        </div>
                        <div class="form-group">
                            <label for="body">Your Story</label>
                            <textarea id="body" name="body" rows="10" required style="width:100%;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;font-family:inherit;resize:vertical;">${post.body}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">New Image (optional)</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            ${post.image ? `<p style="font-size:0.8rem;color:#b0b0ff;margin-top:0.3rem;">Current image will be kept if no new one is selected.</p>` : ''}
                        </div>
                        <button type="submit" class="btn-primary">Update Log</button>
                    </form>
                `;

                document.getElementById('editPostForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const res = await fetch('/CosmicDiary/api/blog/update.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.success) {
                        showToast('Log updated!', 'success');
                        setTimeout(() => window.location.href = '/CosmicDiary/pages/log-view.php?id=' + post.id, 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                });
            });
    }
}

// Build the dashboard summary block for the signed-in user.
if (document.getElementById('userStats')) {
    fetch('/CosmicDiary/api/user/profile.php')
        .then(res => res.json())
        .then(user => {
            const dashboardUsername = document.getElementById('dashboardUsername');
            const dashboardAvatar = document.getElementById('dashboardAvatar');
            if (dashboardUsername) dashboardUsername.textContent = user.username;
            if (dashboardAvatar) dashboardAvatar.src = user.avatar_url;
            document.getElementById('userStats').innerHTML = `
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Bio:</strong> ${user.bio || 'No bio yet. <a href="profile.php" style="color:#00f7ff;">Add one</a>'}</p>
                <p>
                    <strong><a href="/CosmicDiary/pages/explorer-connections.php?id=${user.id}&tab=followers" style="color:#00f7ff;text-decoration:none;">Followers: ${user.follower_count || 0}</a></strong> | 
                    <strong><a href="/CosmicDiary/pages/explorer-connections.php?id=${user.id}&tab=following" style="color:#00f7ff;text-decoration:none;">Following: ${user.following_count || 0}</a></strong>
                </p>
                <p><strong>Log Entries:</strong> ${user.postCount}</p>
                <p><strong>Status:</strong> ${user.is_online ? '<span style="color:#00ff88;">Online now</span>' : 'Last seen ' + user.last_seen_formatted}</p>
                <p><strong>Member since:</strong> ${user.created_at_formatted}</p>
            `;
        });
}

// Render the signed-in user's log list.
if (document.getElementById('userPosts')) {
    fetch('/CosmicDiary/api/user/profile.php')
        .then(res => res.json())
        .then(user => {
            fetch('/CosmicDiary/api/blog/read.php?page=1')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('userPosts');
                    const userPosts = data.posts.filter(post => post.user_id == user.id);
                    if (userPosts.length > 0) {
                        container.innerHTML = userPosts.map(post => `
                            <div class="glass-card" style="margin-bottom:1rem;padding:1rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
                                <div>
                                    <h4><a href="log-view.php?id=${post.id}" style="color:#00f7ff;">${post.title}</a></h4>
                                    <p style="font-size:0.85rem;color:#b0b0ff;">${new Date(post.created_at).toLocaleDateString()}</p>
                                </div>
                                ${post.is_owner ? `
                                    <div style="display:flex;gap:0.3rem;">
                                        <button onclick="window.location.href='log-edit.php?id=${post.id}'" class="btn-edit" style="font-size:0.7rem;padding:0.35rem 0.7rem;"><i class="fas fa-pencil-alt"></i></button>
                                        <button onclick="deletePost(${post.id})" class="btn-delete" style="font-size:0.7rem;padding:0.35rem 0.7rem;"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p>No log entries yet. <a href="log-create.php" style="color:#00f7ff;">Write your first log.</a></p>';
                    }
                });
        });
}