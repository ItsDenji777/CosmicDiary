<?php
/**
 * Interactive planet builder page for creating custom planets.
 */
$pageTitle = 'Planet Builder';
require_once '../includes/session.php';
require_once '../includes/header.php';
?>
<div class="auth-container">
    <div class="glass-card" style="max-width:900px;width:100%;">
        <h2>Planet Builder</h2>
        <div style="display:flex;gap:2rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:300px;">
                <canvas id="planetCanvas" width="1000" height="1000" style="width:100%;max-width:100%;height:auto;display:block;margin:0 auto;"></canvas>
            </div>
            <div style="flex:1;min-width:250px;">
                <div class="form-group">
                    <label for="planetName">Planet Name</label>
                    <input type="text" id="planetName" placeholder="Name your world">
                </div>
                <div class="form-group">
                    <label for="planetBackstory">Backstory</label>
                    <textarea id="planetBackstory" rows="3" placeholder="The history of this world..." style="width:100%;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;font-family:inherit;resize:vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label for="planetSize">Size: <span id="sizeValue">50</span></label>
                    <input type="range" id="planetSize" min="20" max="100" value="50">
                </div>
                <div class="form-group">
                    <label for="planetWater">Land: <span id="waterValue">50</span>%</label>
                    <input type="range" id="planetWater" min="0" max="100" value="50">
                </div>
                <div class="form-group">
                    <label for="planetAtmosphere">Atmosphere: <span id="atmoValue">50</span>%</label>
                    <input type="range" id="planetAtmosphere" min="0" max="100" value="50">
                </div>
                <div class="form-group">
                    <label for="planetMoons">Moons: <span id="moonsValue">0</span></label>
                    <input type="range" id="planetMoons" min="0" max="5" value="0">
                </div>
                <div class="form-group">
                    <label for="planetSpeed">Rotation Speed: <span id="speedValue">5</span></label>
                    <input type="range" id="planetSpeed" min="0" max="20" value="5">
                </div>
                <div class="form-group">
                    <label for="planetRing">Rings</label>
                    <input type="checkbox" id="planetRing">
                </div>
                <div class="form-group">
                    <label for="planetColorLand">Land Color</label>
                    <input type="color" id="planetColorLand" value="#4a9c5d">
                </div>
                <div class="form-group">
                    <label for="planetColorWater">Water Color</label>
                    <input type="color" id="planetColorWater" value="#2a6fdb">
                </div>
                <div class="form-group">
                    <label for="planetColorRing">Ring Color</label>
                    <input type="color" id="planetColorRing" value="#c8c8dc">
                </div>
                <div class="form-group">
                    <label for="planetColorMoon">Moon Color</label>
                    <input type="color" id="planetColorMoon" value="#cccccc">
                </div>
                <button onclick="savePlanet()" class="btn-primary">Launch Planet</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/planet-builder.js"></script>
<?php require_once '../includes/footer.php'; ?>