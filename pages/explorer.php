<?php
/**
 * Public explorer profile page with follow and story controls.
 */
$pageTitle = 'Explorer Profile';
require_once '../includes/header.php';

$explorerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($explorerId === 0 && isset($_GET['username'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_GET['username']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
        $explorerId = $result['id'];
    }
}
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:800px;width:100%;" id="explorerProfile">
        <div class="skeleton" style="height:400px;border-radius:16px;"></div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/blog.js"></script>
<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<script>
const explorerId = <?= $explorerId ?>;
let isFollowing = false;

/**
 * Loads the public profile data for the selected explorer.
 */
function loadExplorerProfile() {
    fetch(`/CosmicDiary/api/user/public-profile.php?id=${explorerId}`)
        .then(res => res.json())
        .then(user => {
            if (!user || !user.id) {
                document.getElementById('explorerProfile').innerHTML = '<p>Explorer not found.</p>';
                return;
            }

            if (user.is_private && !user.is_owner && !user.is_following) {
                document.getElementById('explorerProfile').innerHTML = `
                    <div style="text-align:center;padding:2rem;">
                        <i class="fas fa-lock" style="font-size:3rem;color:#ff00c8;"></i>
                        <h2 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin:1rem 0;">Private Profile</h2>
                        <p style="color:#b0b0ff;">${user.username} has set their profile to private.</p>
                        <button onclick="toggleFollow(${user.id})" id="followBtn" class="btn-follow" style="background:var(--purple);color:white;border:none;padding:0.6rem 1.5rem;border-radius:2rem;font-family:'Orbitron',sans-serif;cursor:pointer;transition:transform 0.2s;margin-top:1.5rem;">
                            <i class="fas fa-user-plus"></i> Request to Follow
                        </button>
                    </div>
                `;
                return;
            }

            isFollowing = user.is_following;
            document.title = 'Cosmic Diary - ' + user.username;

            document.getElementById('explorerProfile').innerHTML = `
                <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                    <div class="avatar-wrapper" style="cursor:pointer;" id="avatarContainer" onclick="event.stopPropagation(); checkUserStories(${user.id})">
                        <img src="${user.avatar_url}" alt="${user.username}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid #00f7ff;" id="profileAvatar" class="profile-avatar-img">
                    </div>
                    <div style="flex:1;">
                        <h1 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin:0;">${user.username}</h1>
                        <p style="font-size:0.8rem;margin-top:0.2rem;">
                            ${user.is_online ? '<span style="color:#00ff88;"><i class="fas fa-circle" style="font-size:0.5rem;"></i> Online now</span>' : `<span style="color:#b0b0ff;"><i class="far fa-clock"></i> Last seen ${user.last_seen_formatted}</span>`}
                        </p>
                        <p style="font-size:0.85rem;color:var(--text-muted);">Member since ${user.created_at_formatted}</p>
                    </div>
                    ${!user.is_owner ? `
                        <button onclick="toggleFollow(${user.id})" id="followBtn" class="btn-follow" style="background:${isFollowing ? 'var(--pink)' : 'var(--purple)'};color:white;border:none;padding:0.6rem 1.5rem;border-radius:2rem;font-family:'Orbitron',sans-serif;cursor:pointer;transition:transform 0.2s;">
                            <i class="fas fa-${isFollowing ? 'user-check' : 'user-plus'}"></i> ${isFollowing ? 'Following' : 'Follow'}
                        </button>
                    ` : ''}
                    ${user.pending_request ? `
                        <div style="display:flex;gap:0.5rem;">
                            <button onclick="handleRequest(${user.pending_request.id}, 'accepted')" class="btn-edit" style="width:auto;"><i class="fas fa-check"></i> Accept</button>
                            <button onclick="handleRequest(${user.pending_request.id}, 'declined')" class="btn-delete" style="width:auto;"><i class="fas fa-times"></i> Decline</button>
                        </div>
                    ` : ''}
                </div>
                <div class="stats-bar" style="padding:1rem 0;gap:2rem;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:1.5rem;">
                    <div class="stat-item" style="cursor:pointer;" onclick="window.location.href='explorer-connections.php?id=${user.id}&tab=followers'">
                        <span class="stat-number" style="font-size:1.5rem;">${user.follower_count}</span>
                        <span class="stat-label">Followers</span>
                    </div>
                    <div class="stat-item" style="cursor:pointer;" onclick="window.location.href='explorer-connections.php?id=${user.id}&tab=following'">
                        <span class="stat-number" style="font-size:1.5rem;">${user.following_count}</span>
                        <span class="stat-label">Following</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" style="font-size:1.5rem;">${user.post_count}</span>
                        <span class="stat-label">Logs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" style="font-size:1.5rem;">${user.planet_count}</span>
                        <span class="stat-label">Planets</span>
                    </div>
                </div>
                <div id="explorerPostsSection">
                    <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:1rem;">Captain's Logs</h3>
                    <div id="explorerPosts"></div>
                </div>
                <div id="explorerPlanetsSection" style="margin-top:1.5rem;">
                    <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:1rem;">Planets Discovered</h3>
                    <div id="explorerPlanets" class="blog-grid"></div>
                </div>
            `;

            loadExplorerPosts();
            loadExplorerPlanets();
            highlightStoryRing(explorerId);
        });
}

/**
 * Sends a follow or unfollow request for the selected user.
 */
function toggleFollow(userId) {
    const formData = new FormData();
    formData.append('following_id', userId);

    fetch('/CosmicDiary/api/user/follow.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.requested) {
                    showToast('Follow request sent!', 'success');
                    const btn = document.getElementById('followBtn');
                    if (btn) {
                        btn.innerHTML = '<i class="fas fa-clock"></i> Requested';
                        btn.style.background = 'rgba(255,255,255,0.1)';
                    }
                    return;
                }
                isFollowing = data.following;
                const btn = document.getElementById('followBtn');
                if (btn) {
                    btn.innerHTML = `<i class="fas fa-${isFollowing ? 'user-check' : 'user-plus'}"></i> ${isFollowing ? 'Following' : 'Follow'}`;
                    btn.style.background = isFollowing ? 'var(--pink)' : 'var(--purple)';
                }
                
                const followerStat = document.querySelector('.stat-item .stat-number');
                if (followerStat && data.followerCount !== undefined) {
                    const statNumbers = document.querySelectorAll('.stat-number');
                    if (statNumbers[0]) statNumbers[0].textContent = data.followerCount;
                }
            } else {
                showToast(data.message, 'error');
            }
        });
}

/**
 * Loads the explorer's recent log entries into the page.
 */
function loadExplorerPosts() {
    const container = document.getElementById('explorerPosts');
    if (!container) return;

    fetch(`/CosmicDiary/api/blog/read.php?page=1&limit=10`)
        .then(res => res.json())
        .then(data => {
            const userPosts = data.posts.filter(post => post.user_id == explorerId);
            if (userPosts.length === 0) {
                container.innerHTML = '<p>No log entries yet.</p>';
                return;
            }
            container.innerHTML = userPosts.map(post => `
                <div class="glass-card" style="margin-bottom:0.75rem;padding:1rem;background:rgba(255,255,255,0.03);">
                    <h4><a href="log-view.php?id=${post.id}" style="color:#00f7ff;">${post.title}</a></h4>
                    <p style="font-size:0.8rem;color:#b0b0ff;">${new Date(post.created_at).toLocaleDateString()}</p>
                    <p style="font-size:0.85rem;margin-top:0.3rem;">${post.body.substring(0, 200)}...</p>
                </div>
            `).join('');
        });
}

/**
 * Loads the explorer's planets into the profile gallery.
 */
function loadExplorerPlanets() {
    const container = document.getElementById('explorerPlanets');
    if (!container) return;

    fetch(`/CosmicDiary/api/planet/load.php?user_id=${explorerId}`)
        .then(res => res.json())
        .then(planets => {
            if (planets.length === 0) {
                container.innerHTML = '<p>No planets discovered yet.</p>';
                return;
            }
            container.innerHTML = planets.map(planet => `
                <div class="glass-card post-card" style="text-align:center;cursor:pointer;background:rgba(255,255,255,0.03);" onclick="window.location.href='planet-view.php?id=${planet.id}'">
                    <canvas id="explorer-planet-${planet.id}" width="200" height="200" style="width:100%;max-width:200px;"></canvas>
                    <h4 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">${planet.name}</h4>
                    <p class="post-meta">${planet.like_count} likes</p>
                </div>
            `).join('');

            planets.forEach(planet => {
                const miniCanvas = document.getElementById(`explorer-planet-${planet.id}`);
                if (miniCanvas && typeof drawPlanetOnCtx !== 'undefined' && typeof generateGalleryPatches !== 'undefined') {
                    const config = {
                        size: planet.size,
                        water: planet.water,
                        atmosphere: planet.atmosphere,
                        colorLand: planet.color_land,
                        colorWater: planet.color_water,
                        colorRing: planet.color_ring || '#c8c8dc',
                        colorMoon: planet.color_moon || '#cccccc',
                        ring: planet.ring == 1,
                        moons: planet.moons,
                        _patches: generateGalleryPatches(planet.size, planet.water)
                    };
                    drawPlanetOnCtx(miniCanvas.getContext('2d'), 200, 200, config, Math.random() * Math.PI * 2);
                }
            });
        });
}

/**
 * Approves or rejects a follow request from this page.
 */
function handleRequest(requestId, status) {
    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('status', status);
    fetch('/CosmicDiary/api/user/follow-requests.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) { showToast(data.message, 'success'); loadExplorerProfile(); }
            else { showToast(data.message, 'error'); }
        });
}

/**
 * Checks whether the user currently has any active stories.
 */
function checkUserStories(userId) {
    fetch('/CosmicDiary/api/stories/read.php')
        .then(res => res.json())
        .then(stories => {
            const userStories = stories.filter(s => s.user_id == userId);
            if (userStories.length > 0) {
                currentStories = userStories;
                currentStoryIndex = 0;
                
                if (document.getElementById('storyViewer')) {
                    document.getElementById('storyViewer').remove();
                }
                
                const progressChunks = currentStories.map((_, i) => `
                    <div class="story-progress-chunk">
                        <div class="story-progress-chunk-fill" id="progressFill${i}"></div>
                    </div>
                `).join('');
                
                const viewer = document.createElement('div');
                viewer.className = 'story-viewer open';
                viewer.id = 'storyViewer';
                viewer.innerHTML = `
                    <div class="story-progress" id="storyProgressBar">${progressChunks}</div>
                    <button class="story-viewer-close" onclick="closeStoryViewer()">&times;</button>
                    ${currentStories[0].is_mine ? `<button class="story-viewer-delete" onclick="deleteCurrentStory()"><i class="fas fa-trash-alt"></i></button>` : ''}
                    <div class="story-viewer-user">
                        <img src="${currentStories[0].avatar_url}">
                        <span>${currentStories[0].username}</span>
                    </div>
                    <div class="story-tap-left" onclick="prevStory()"></div>
                    <div class="story-tap-right" onclick="nextStory()"></div>
                    <div class="story-viewer-content" id="storyContent"></div>
                `;
                document.body.appendChild(viewer);
                showCurrentStory();
            }
        });
}

/**
 * Highlights the avatar ring when stories are available.
 */
function highlightStoryRing(userId) {
    fetch('/CosmicDiary/api/stories/read.php')
        .then(res => res.json())
        .then(stories => {
            const userStories = stories.filter(s => s.user_id == userId);
            const avatar = document.getElementById('profileAvatar');
            const container = document.getElementById('avatarContainer');
            if (avatar && container) {
                if (userStories.length > 0) {
                    container.classList.add('has-story-ring');
                    avatar.style.border = 'none';
                    container.title = 'View Story';
                } else {
                    container.classList.remove('has-story-ring');
                    avatar.style.border = '2px solid #00f7ff';
                    container.title = '';
                }
            }
        });
}

loadExplorerProfile();
</script>
<?php require_once '../includes/footer.php'; ?>