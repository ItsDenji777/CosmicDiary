<?php
/**
 * Landing page that shows site stats, recent logs, and featured planets.
 */
$pageTitle = 'Home';
require_once '../includes/header.php';
require_once '../includes/functions.php';

$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalPosts = $conn->query("SELECT COUNT(*) as total FROM posts WHERE is_published = 1")->fetch_assoc()['total'];
$totalPlanets = $conn->query("SELECT COUNT(*) as total FROM planets")->fetch_assoc()['total'];
$onlineUsers = $conn->query("SELECT COUNT(*) as total FROM users WHERE last_seen IS NOT NULL AND TIMESTAMPDIFF(SECOND, last_seen, NOW()) < 60")->fetch_assoc()['total'];

$recentPosts = $conn->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.is_published = 1 ORDER BY p.created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
$featuredPlanets = $conn->query("SELECT p.*, u.username, (SELECT COUNT(*) FROM planet_likes WHERE planet_id = p.id) as like_count FROM planets p JOIN users u ON p.user_id = u.id ORDER BY like_count DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>
<div class="hero">
    <img src="<?= BASE_URL ?>assets/images/hero-logo.svg" alt="Cosmic Diary" class="hero-logo" style="width:150px;height:150px;">
    <h1>Welcome to Cosmic Diary</h1>
    <p><span id="typewriter"></span><span class="cursor">|</span></p>
</div>
<?php if (isLoggedIn()): ?>
<div class="stories-bar" id="storiesBar">
    <div class="stories-container" id="storiesContainer">
        <div class="skeleton" style="width:70px;height:70px;border-radius:50%;flex-shrink:0;"></div>
        <div class="skeleton" style="width:70px;height:70px;border-radius:50%;flex-shrink:0;"></div>
        <div class="skeleton" style="width:70px;height:70px;border-radius:50%;flex-shrink:0;"></div>
    </div>
</div>
<?php endif; ?>
<div class="stats-bar">
    <div class="stat-item">
        <span class="stat-number" data-target="<?= $onlineUsers ?>">0</span>
        <span class="stat-label">Online Now</span>
    </div>
    <div class="stat-item">
        <span class="stat-number" data-target="<?= $totalUsers ?>">0</span>
        <span class="stat-label">Explorers</span>
    </div>
    <div class="stat-item">
        <span class="stat-number" data-target="<?= $totalPosts ?>">0</span>
        <span class="stat-label">Log Entries</span>
    </div>
    <div class="stat-item">
        <span class="stat-number" data-target="<?= $totalPlanets ?>">0</span>
        <span class="stat-label">Planets</span>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:2rem;">
    <div style="margin-bottom:3rem;">
        <h2 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:1.5rem;"><i class="fas fa-satellite-dish"></i> Recent Captain's Logs</h2>
        <div class="blog-grid">
            <?php foreach ($recentPosts as $post): ?>
                <div class="glass-card post-card" onclick="window.location.href='log-view.php?id=<?= $post['id'] ?>'" style="cursor:pointer;">
                    <?php if ($post['image']): ?>
                        <img src="<?= BASE_URL ?>uploads/<?= $post['image'] ?>" alt="<?= $post['title'] ?>" class="post-image">
                    <?php endif; ?>
                    <h3 style="color:#00f7ff;"><?= htmlspecialchars($post['title']) ?></h3>
                    <p class="post-meta">By <?= htmlspecialchars($post['username']) ?> | <?= date('M d, Y', strtotime($post['created_at'])) ?></p>
                    <p class="post-excerpt"><?= htmlspecialchars(substr($post['body'], 0, 150)) ?>...</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        <h2 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:1.5rem;"><i class="fas fa-globe-americas"></i> Featured Planets</h2>
        <div class="blog-grid">
            <?php foreach ($featuredPlanets as $planet): ?>
                <div class="glass-card post-card" style="text-align:center;cursor:pointer;" onclick="window.location.href='planet-view.php?id=<?= $planet['id'] ?>'">
                    <canvas id="home-planet-<?= $planet['id'] ?>" width="200" height="200" style="width:100%;max-width:200px;"></canvas>
                    <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;"><?= htmlspecialchars($planet['name']) ?></h3>
                    <p class="post-meta">By <?= htmlspecialchars($planet['username']) ?> | <?= $planet['like_count'] ?> likes</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    <?php foreach ($featuredPlanets as $planet): ?>
    const canvas<?= $planet['id'] ?> = document.getElementById('home-planet-<?= $planet['id'] ?>');
    if (canvas<?= $planet['id'] ?>) {
        const config = {
            size: <?= $planet['size'] ?>,
            water: <?= $planet['water'] ?>,
            atmosphere: <?= $planet['atmosphere'] ?>,
            colorLand: '<?= $planet['color_land'] ?>',
            colorWater: '<?= $planet['color_water'] ?>',
            colorRing: '<?= $planet['color_ring'] ?? '#c8c8dc' ?>',
            colorMoon: '<?= $planet['color_moon'] ?? '#cccccc' ?>',
            ring: <?= $planet['ring'] ?>,
            moons: <?= $planet['moons'] ?>,
            _patches: generateGalleryPatches(<?= $planet['size'] ?>, <?= $planet['water'] ?>)
        };
        drawPlanetOnCtx(canvas<?= $planet['id'] ?>.getContext('2d'), 200, 200, config, Math.random() * Math.PI * 2);
    }
    <?php endforeach; ?>
});

const text = "Your journey begins here.";
const speed = 60;
let i = 0;
/**
 * Animates the landing headline one character at a time.
 */
function typeWriter() {
    if (i < text.length) {
        document.getElementById('typewriter').textContent += text.charAt(i);
        i++;
        setTimeout(typeWriter, speed);
    }
}
document.addEventListener('DOMContentLoaded', typeWriter);

/**
 * Animates the homepage stat counters into view.
 */
function animateStats() {
    const stats = document.querySelectorAll('.stat-number');
    const targets = [];
    const durations = [];
    const start = performance.now();
    
    stats.forEach(stat => {
        targets.push(parseInt(stat.getAttribute('data-target')));
    });
    
    const maxTarget = Math.max(...targets);
    const baseDuration = 2500;
    
    targets.forEach((target, i) => {
        durations[i] = (target / maxTarget) * baseDuration;
    });
    
    /**
     * Advances a single homepage stat counter frame.
     */
    function update(now) {
        let allDone = true;
        
        stats.forEach((stat, i) => {
            const elapsed = now - start;
            const progress = Math.min(elapsed / durations[i], 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(eased * targets[i]);
            
            if (progress < 1) {
                allDone = false;
            }
            
            stat.textContent = current < targets[i] ? current : targets[i];
        });
        
        if (!allDone) {
            requestAnimationFrame(update);
        } else {
            stats.forEach((stat, i) => {
                stat.textContent = targets[i];
            });
        }
    }
    
    requestAnimationFrame(update);
}

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            observer.disconnect();
        }
    });
});

const statsBar = document.querySelector('.stats-bar');
if (statsBar) observer.observe(statsBar);

let onlineRefreshInterval;

/**
 * Refreshes the simulated online count on the homepage.
 */
function refreshOnlineCount() {
    fetch('/CosmicDiary/api/cron/simulate-activity.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const onlineStat = document.querySelector('.stat-number');
                if (onlineStat) {
                    onlineStat.textContent = data.online;
                }
            }
        })
        .catch(() => {});
}
if (window._onlineInterval) clearInterval(window._onlineInterval);
refreshOnlineCount();

window._onlineInterval = setInterval(refreshOnlineCount, 10000);

</script>
<?php require_once '../includes/footer.php'; ?>