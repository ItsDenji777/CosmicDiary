<?php
/**
 * About page content for the Cosmic Diary project.
 */
$pageTitle = 'Mission Control';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:800px;width:100%;">
        <h1 style="font-family:'Orbitron',sans-serif;color:#00f7ff;text-align:center;margin-bottom:1rem;">Mission Control</h1>
        <p style="text-align:center;color:#b0b0ff;margin-bottom:2rem;">The story behind the stars.</p>
        
        <div style="line-height:1.8;margin-bottom:2rem;">
            <p>Welcome to <strong style="color:#00f7ff;">Cosmic Diary</strong>, the galaxy's premier platform for explorers, dreamers, and world-builders. Our mission began in the year 2187 when a small crew of intergalactic cartographers realized the universe needed a place to document its infinite wonders.</p>
            <p style="margin-top:1rem;">Here, you can chronicle your cosmic journeys through the <strong><a href="../pages/captain-log.php" style="color:#ff00c8; text-decoration: none;">Captain's Log</a></strong>, create entire worlds with the <strong><a href="../pages/planet-builder.php" style="color:#7b2fff; text-decoration: none;">Planet Builder</a></strong>, and connect with fellow travelers across the stars.</p>
        </div>

        <h2 style="font-family:'Orbitron',sans-serif;color:#00f7ff;text-align:center;margin-bottom:1.5rem;">The Crew</h2>
        <div class="blog-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
            <div class="glass-card" style="text-align:center;padding:1.5rem;">
                <i class="fas fa-rocket" style="font-size:3rem;color:#00f7ff;"></i>
                <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">Captain Denji</h3>
                <p style="font-size:0.85rem;color:#b0b0ff;">Founder & Navigator</p>
            </div>
            <div class="glass-card" style="text-align:center;padding:1.5rem;">
                <i class="fas fa-microscope" style="font-size:3rem;color:#ff00c8;"></i>
                <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">Dr. Stellar</h3>
                <p style="font-size:0.85rem;color:#b0b0ff;">Chief Scientist</p>
            </div>
            <div class="glass-card" style="text-align:center;padding:1.5rem;">
                <i class="fas fa-globe" style="font-size:3rem;color:#7b2fff;"></i>
                <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">Zephyr</h3>
                <p style="font-size:0.85rem;color:#b0b0ff;">Planet Architect</p>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>