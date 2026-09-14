const planetCanvas = document.getElementById('planetCanvas');
let galleryAnimationId = null;
let galleryRunning = false;
let currentPlanet = null;


// Draws a planet preview onto the supplied canvas context.
function drawPlanetOnCtx(context, canvasW, canvasH, config, angle) {
    const size = config.size;
    const landPercent = config.water;
    const atmosphere = config.atmosphere;
    
    // Checks whether a color string is a valid six-digit hex code.
    const isValidHex = (c) => typeof c === 'string' && /^#[0-9a-fA-F]{6}$/.test(c);
    const colorLand = isValidHex(config.colorLand) ? config.colorLand : '#4a9c5d';
    const colorWater = isValidHex(config.colorWater) ? config.colorWater : '#2a6fdb';
    const colorRing = isValidHex(config.colorRing) ? config.colorRing : '#c8c8dc';
    const colorMoon = isValidHex(config.colorMoon) ? config.colorMoon : '#cccccc';
    const ring = config.ring;
    const moons = config.moons;

    const cx = canvasW / 2;
    const cy = canvasH / 2;
    const maxRadius = Math.min(canvasW, canvasH) * 0.25;
    const radius = (size / 100) * maxRadius;

    context.clearRect(0, 0, canvasW, canvasH);

    for (let i = 0; i < moons; i++) {
        const moonAngle = (i / Math.max(moons, 1)) * Math.PI * 2 + angle * 0.2;
        const moonOrbitX = radius * 1.8;
        const moonOrbitY = radius * 1.4;
        const moonX = cx + Math.cos(moonAngle) * moonOrbitX;
        const moonY = cy + Math.sin(moonAngle) * moonOrbitY;
        const moonSize = radius * 0.1;

        context.beginPath();
        context.arc(moonX, moonY, moonSize, 0, Math.PI * 2);
        const moonGrad = context.createRadialGradient(moonX - moonSize * 0.3, moonY - moonSize * 0.3, moonSize * 0.1, moonX, moonY, moonSize);
        moonGrad.addColorStop(0, colorMoon);
        moonGrad.addColorStop(1, '#111122');
        context.fillStyle = moonGrad;
        context.fill();
    }

    if (ring) {
        context.save();
        context.beginPath();
        context.ellipse(cx, cy, radius * 1.55, radius * 0.32, 0, 0, Math.PI * 2);
        context.strokeStyle = colorRing;
        context.lineWidth = radius * 0.12;
        context.globalAlpha = 0.7;
        context.stroke();
        context.globalAlpha = 1;
        context.restore();
    }

    const tiltOffset = Math.sin(angle * 0.5) * radius * 0.05;

    const baseGradient = context.createRadialGradient(cx - radius * 0.3 + tiltOffset, cy - radius * 0.4, radius * 0.05, cx + tiltOffset, cy + radius * 0.1, radius);
    baseGradient.addColorStop(0, colorWater);
    baseGradient.addColorStop(0.7, colorWater);
    baseGradient.addColorStop(1, '#050515');
    context.beginPath();
    context.arc(cx, cy, radius, 0, Math.PI * 2);
    context.fillStyle = baseGradient;
    context.fill();

    context.save();
    context.beginPath();
    context.arc(cx, cy, radius, 0, Math.PI * 2);
    context.clip();

    const patches = config._patches || [];
    patches.forEach(patch => {
        const rotatedLon = patch.lon + angle;
        const x3d = Math.cos(patch.lat) * Math.sin(rotatedLon);
        const y3d = Math.sin(patch.lat);
        const z3d = Math.cos(patch.lat) * Math.cos(rotatedLon);

        if (z3d > -0.2) {
            const projX = cx + x3d * radius;
            const projY = cy + y3d * radius;
            const projSize = patch.size * radius * (0.6 + z3d * 0.4);
            context.beginPath();
            context.arc(projX, projY, projSize, 0, Math.PI * 2);
            context.fillStyle = colorLand;
            context.fill();
        }
    });

    const shadowGradient = context.createRadialGradient(cx + radius * 0.3, cy + radius * 0.3, radius * 0.4, cx, cy, radius);
    shadowGradient.addColorStop(0, 'rgba(0,0,0,0.6)');
    shadowGradient.addColorStop(0.6, 'rgba(0,0,0,0.1)');
    shadowGradient.addColorStop(1, 'rgba(0,0,0,0)');
    context.beginPath();
    context.arc(cx, cy, radius, 0, Math.PI * 2);
    context.fillStyle = shadowGradient;
    context.fill();

    context.restore();

    context.beginPath();
    context.arc(cx, cy, radius, 0, Math.PI * 2);
    context.strokeStyle = `rgba(255,255,255,${atmosphere / 100 * 0.6})`;
    context.lineWidth = radius * 0.06 * (atmosphere / 100) + 1;
    context.stroke();

    const atmoGlow = context.createRadialGradient(cx, cy, radius * 0.92, cx, cy, radius * 1.1);
    atmoGlow.addColorStop(0, 'rgba(100,180,255,0)');
    atmoGlow.addColorStop(0.5, `rgba(100,180,255,${atmosphere / 100 * 0.3})`);
    atmoGlow.addColorStop(1, 'rgba(100,180,255,0)');
    context.beginPath();
    context.arc(cx, cy, radius * 1.1, 0, Math.PI * 2);
    context.fillStyle = atmoGlow;
    context.fill();

    if (ring) {
        context.save();
        context.beginPath();
        context.arc(cx, cy, radius, 0, Math.PI * 2);
        context.clip();
        context.beginPath();
        context.ellipse(cx, cy, radius * 1.55, radius * 0.32, 0, Math.PI, Math.PI * 2);
        context.strokeStyle = colorRing;
        context.lineWidth = radius * 0.12;
        context.globalAlpha = 0.5;
        context.stroke();
        context.globalAlpha = 1;
        context.restore();
    }
}

if (planetCanvas) {
    const ctx = planetCanvas.getContext('2d');

    const sizeInput = document.getElementById('planetSize');
    const waterInput = document.getElementById('planetWater');
    const atmoInput = document.getElementById('planetAtmosphere');
    const moonsInput = document.getElementById('planetMoons');
    const speedInput = document.getElementById('planetSpeed');
    const ringInput = document.getElementById('planetRing');
    const colorLandInput = document.getElementById('planetColorLand');
    const colorWaterInput = document.getElementById('planetColorWater');
    const colorRingInput = document.getElementById('planetColorRing');
    const colorMoonInput = document.getElementById('planetColorMoon');

    let rotationAngle = 0;
    let landPatches = [];

    // Creates randomized land masses for the planet surface.
    function generateLandPatches(size, landPercent) {
        const patches = [];
        const patchCount = 30 + Math.floor(landPercent * 0.5);
        const baseSeed = size * 37 + landPercent * 13;

        for (let i = 0; i < patchCount; i++) {
            const lat = (Math.random() - 0.5) * Math.PI * 0.85;
            const lon = Math.random() * Math.PI * 2;
            const patchSize = 0.03 + Math.random() * 0.12 * (landPercent / 60);
            patches.push({ lat, lon, size: patchSize, seed: i + baseSeed });
        }

        const continentCount = Math.floor(landPercent / 15);
        for (let c = 0; c < continentCount; c++) {
            const centerLat = (Math.random() - 0.5) * Math.PI * 0.7;
            const centerLon = Math.random() * Math.PI * 2;
            for (let i = 0; i < 25; i++) {
                const offsetLat = (Math.random() - 0.5) * 0.5;
                const offsetLon = (Math.random() - 0.5) * 0.5;
                patches.push({
                    lat: centerLat + offsetLat,
                    lon: centerLon + offsetLon,
                    size: 0.04 + Math.random() * 0.1 * (landPercent / 60),
                    seed: c * 100 + i + baseSeed
                });
            }
        }

        return patches;
    }

    landPatches = generateLandPatches(50, 50);

    [sizeInput, waterInput].forEach(el => {
        el.addEventListener('input', () => {
            landPatches = generateLandPatches(parseInt(sizeInput.value), parseInt(waterInput.value));
        });
    });

    // Builds the live planet preview from the current controls.
    function drawPlanet() {
        const config = {
            size: parseInt(sizeInput.value),
            water: parseInt(waterInput.value),
            atmosphere: parseInt(atmoInput.value),
            colorLand: colorLandInput.value,
            colorWater: colorWaterInput.value,
            colorRing: colorRingInput.value,
            colorMoon: colorMoonInput.value,
            ring: ringInput.checked,
            moons: parseInt(moonsInput.value),
            _patches: landPatches
        };

        drawPlanetOnCtx(ctx, planetCanvas.width, planetCanvas.height, config, rotationAngle);

        document.getElementById('sizeValue').textContent = config.size;
        document.getElementById('waterValue').textContent = config.water;
        document.getElementById('atmoValue').textContent = config.atmosphere;
        document.getElementById('moonsValue').textContent = config.moons;
        document.getElementById('speedValue').textContent = speedInput.value;
    }

    let lastTime = 0;
    // Animates the rotating preview planet.
    function animate(timestamp) {
        if (lastTime === 0) {
            lastTime = timestamp;
            requestAnimationFrame(animate);
            return;
        }
        const delta = (timestamp - lastTime) / 1000;
        lastTime = timestamp;
        const speedRadPerSec = ((parseInt(speedInput.value) || 0) / 100) * Math.PI * 2;
        rotationAngle += speedRadPerSec * delta;
        drawPlanet();
        requestAnimationFrame(animate);
    }

    [sizeInput, waterInput, atmoInput, moonsInput, speedInput, ringInput, colorLandInput, colorWaterInput, colorRingInput, colorMoonInput].forEach(el => {
        el.addEventListener('input', () => drawPlanet());
    });

    requestAnimationFrame(animate);
}

// Saves the current planet configuration.
function savePlanet() {
    const name = document.getElementById('planetName').value;
    if (!name) {
        showToast('Name your planet first.', 'error');
        return;
    }

    const data = {
        name: name,
        backstory: document.getElementById('planetBackstory').value,
        size: parseInt(document.getElementById('planetSize').value),
        water: parseInt(document.getElementById('planetWater').value),
        atmosphere: parseInt(document.getElementById('planetAtmosphere').value),
        colorLand: document.getElementById('planetColorLand').value,
        colorWater: document.getElementById('planetColorWater').value,
        colorRing: document.getElementById('planetColorRing').value,
        colorMoon: document.getElementById('planetColorMoon').value,
        moons: parseInt(document.getElementById('planetMoons').value),
        ring: document.getElementById('planetRing').checked ? 1 : 0,
        rotationSpeed: parseInt(document.getElementById('planetSpeed').value)
    };

    fetch('/CosmicDiary/api/planet/save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('celebrate', 'planet');
            showToast('Planet launched into the galaxy!', 'success');
            setTimeout(() => window.location.href = '/CosmicDiary/pages/planet-gallery.php', 500);
        } else {
            showToast(data.message, 'error');
        }
    });
}

// Creates lighter geometry data for gallery thumbnails.
function generateGalleryPatches(size, landPercent) {
    const patches = [];
    const patchCount = 20 + Math.floor(landPercent * 0.3);
    for (let i = 0; i < patchCount; i++) {
        const lat = (Math.random() - 0.5) * Math.PI * 0.85;
        const lon = Math.random() * Math.PI * 2;
        patches.push({ lat, lon, size: 0.04 + Math.random() * 0.08 * (landPercent / 60), seed: i });
    }
    return patches;
}

let galleryPage = 1;
let galleryLoading = false;
let galleryHasMore = true;
let galleryAllLoaded = false;

// Loads planets into the public gallery view.
function loadGallery(page = 1) {
    if (galleryLoading || !galleryHasMore || galleryAllLoaded) return;
    galleryLoading = true;
    
    const grid = document.getElementById('planetGrid');
    if (!grid) {
        galleryLoading = false;
        return;
    }

    if (galleryAnimationId) {
        cancelAnimationFrame(galleryAnimationId);
        galleryAnimationId = null;
    }

    fetch(`/CosmicDiary/api/planet/load.php?page=${page}&limit=6`)
        .then(res => res.json())
        .then(planets => {
            if (page === 1 && planets.length > 0) {
                grid.innerHTML = '';
                galleryAllLoaded = false;
                galleryHasMore = true;
            }
            
            if (planets.length === 0) {
                galleryAllLoaded = true;
                galleryHasMore = false;
                if (page === 1) grid.innerHTML = '<p>No planets discovered yet. <a href="planet-builder.php" style="color:#00f7ff;">Create the first one.</a></p>';
                galleryLoading = false;
                return;
            }
            
            if (planets.length < 6) {
                galleryAllLoaded = true;
                galleryHasMore = false;
            }
            
            planets.forEach(planet => {
                if (document.getElementById(`gallery-planet-${planet.id}`)) return;
                
                const card = document.createElement('div');
                card.className = 'glass-card post-card';
                card.style.cssText = 'text-align:center;cursor:pointer;';
                card.setAttribute('data-planet-id', planet.id);
                card.onclick = () => window.location.href = 'planet-view.php?id=' + planet.id;
                card.innerHTML = `
                    <canvas id="gallery-planet-${planet.id}" width="200" height="200" style="width:100%;max-width:200px;display:block;"></canvas>
                    <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">${planet.name}</h3>
                    <p class="post-meta">By <a href="explorer.php?id=${planet.user_id}" style="color:#ff00c8;text-decoration:none;" onclick="event.stopPropagation();">${planet.username}</a></p>
                    <p class="post-meta">${planet.like_count} likes</p>
                    <button onclick="event.stopPropagation(); toggleLike(${planet.id})" class="btn-like" style="background:${planet.liked ? '#ff00c8' : 'rgba(255,255,255,0.1)'};color:white;border:1px solid rgba(255,255,255,0.2);padding:0.4rem 1rem;border-radius:2rem;cursor:pointer;font-family:'Orbitron',sans-serif;font-size:0.8rem;"><i class="${planet.liked ? 'fas' : 'far'} fa-heart"></i> ${planet.liked ? 'Liked' : 'Like'}</button>
                `;
                grid.appendChild(card);
                
                card._planetData = {
                    size: planet.size, water: planet.water, atmosphere: planet.atmosphere,
                    colorLand: planet.color_land, colorWater: planet.color_water,
                    colorRing: planet.color_ring || '#c8c8dc', colorMoon: planet.color_moon || '#cccccc',
                    ring: planet.ring == 1, moons: planet.moons,
                    _patches: generateGalleryPatches(planet.size, planet.water)
                };
            });
            
            galleryPage = page + 1;
            galleryLoading = false;
            
            drawVisiblePlanets();
        })
        .catch(() => {
            galleryLoading = false;
        });
}

// Renders gallery thumbnails only when cards enter view.
function drawVisiblePlanets() {
    const cards = document.querySelectorAll('#planetGrid .post-card');
    cards.forEach(card => {
        if (card._drawn) return;
        const rect = card.getBoundingClientRect();
        if (rect.top < window.innerHeight + 400 && rect.bottom > -400) {
            const planetId = card.getAttribute('data-planet-id');
            const miniCanvas = document.getElementById(`gallery-planet-${planetId}`);
            if (miniCanvas && card._planetData) {
                drawPlanetOnCtx(miniCanvas.getContext('2d'), 200, 200, card._planetData, Math.random() * Math.PI * 2);
                card._drawn = true;
            }
        }
    });
}

let scrollTimeout;
if (document.getElementById('planetGrid')) {
    loadGallery();
    
    window.addEventListener('scroll', () => {
        const grid = document.getElementById('planetGrid');
        if (!grid) return;
        
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            drawVisiblePlanets();
            
            if (galleryAllLoaded || galleryLoading) return;
            
            const cards = grid.querySelectorAll('.post-card');
            if (cards.length === 0) return;
            const lastCard = cards[cards.length - 1];
            
            const rect = lastCard.getBoundingClientRect();
            if (rect.bottom <= window.innerHeight + 300) {
                loadGallery(galleryPage);
            }
        }, 100);
    });
}

// Likes or unlikes a planet from the gallery or detail view.
function toggleLike(planetId) {
    const formData = new FormData();
    formData.append('planet_id', planetId);
    fetch('/CosmicDiary/api/planet/like.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const allLikeBtns = document.querySelectorAll('.btn-like');
                allLikeBtns.forEach(btn => {
                    const onclick = btn.getAttribute('onclick') || '';
                    if (onclick.includes(`toggleLike(${planetId})`)) {
                        const liked = data.liked !== undefined ? data.liked : !btn.querySelector('.fa-heart')?.classList.contains('fas');
                        btn.style.background = liked ? '#ff00c8' : 'rgba(255,255,255,0.1)';
                        btn.innerHTML = `<i class="${liked ? 'fas' : 'far'} fa-heart"></i> ${liked ? 'Liked' : 'Like'}`;
                    }
                });
                const singlePlanet = document.getElementById('singlePlanet');
                if (singlePlanet) {
                    location.reload();
                }
            }
        });
}

// Deletes a planet after confirmation.
function deletePlanet(id) {
    showConfirm('Destroy Planet', 'This planet will be permanently destroyed. This cannot be undone.', 'fa-trash-alt', () => {
        const formData = new FormData();
        formData.append('id', id);
        fetch('/CosmicDiary/api/planet/delete.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Planet destroyed.', 'success');
                    setTimeout(() => window.location.href = '/CosmicDiary/pages/planet-gallery.php', 1000);
                } else {
                    showToast(data.message, 'error');
                }
            });
    });
}

// Loads the current user's planet list into the dashboard.
function loadUserPlanets() {
    const userPlanets = document.getElementById('userPlanets');
    if (!userPlanets) return;
    fetch('/CosmicDiary/api/user/profile.php')
        .then(res => res.json())
        .then(user => {
            fetch(`/CosmicDiary/api/planet/load.php?user_id=${user.id}`)
                .then(res => res.json())
                .then(planets => {
                    if (planets.length === 0) {
                        userPlanets.innerHTML = '<p>No planets created yet. <a href="planet-builder.php" style="color:#00f7ff;">Build your first world.</a></p>';
                        return;
                    }
                    userPlanets.innerHTML = planets.map(planet => `
                        <div class="glass-card post-card" style="text-align:center;cursor:pointer;" onclick="window.location.href='planet-view.php?id=${planet.id}'">
                            <canvas id="dash-planet-${planet.id}" width="150" height="150" style="width:100%;max-width:150px;"></canvas>
                            <h4 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-top:0.5rem;">${planet.name}</h4>
                            <p class="post-meta">${planet.like_count} likes</p>
                            <button onclick="event.stopPropagation(); deletePlanet(${planet.id})" class="btn-delete" style="margin-top:0.5rem;"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    `).join('');

                    planets.forEach(planet => {
                        const miniCanvas = document.getElementById(`dash-planet-${planet.id}`);
                        if (miniCanvas) {
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
                            drawPlanetOnCtx(miniCanvas.getContext('2d'), 150, 150, config, Math.random() * Math.PI * 2);
                        }
                    });
                });
        });
}

if (document.getElementById('userPlanets')) {
    loadUserPlanets();
}

const planetViewParams = new URLSearchParams(window.location.search);
const planetId = planetViewParams.get('id');
if (planetId && document.getElementById('singlePlanet')) {
    fetch(`/CosmicDiary/api/planet/load.php?id=${planetId}`)
        .then(res => res.json())
        .then(planet => {
            currentPlanet = planet;
            if (!planet) {
                document.getElementById('singlePlanet').innerHTML = '<p>Planet not found.</p>';
                return;
            }
            document.getElementById('singlePlanet').innerHTML = `
                <div class="action-bar">
                    <div class="action-bar-left">
                        <a href="planet-gallery.php" style="color:#00f7ff;text-decoration:none;font-family:'Orbitron',sans-serif;">&larr; Back to Galaxy</a>
                    </div>
                    <div class="action-bar-right">
                        ${planet.is_owner ? `<button onclick="deletePlanet(${planet.id})" class="btn-delete"><i class="fas fa-trash-alt"></i> Destroy Planet</button>` : ''}
                        <div style="position:relative;display:inline-block;">
                            <button onclick="toggleExportDropdown(event)" class="btn-export" style="background:var(--purple);color:white;border:none;padding:0.5rem 1rem;border-radius:8px;font-family:'Orbitron',sans-serif;font-size:0.8rem;cursor:pointer;"><i class="fas fa-download"></i> Download <i class="fas fa-chevron-down"></i></button>
                            <div id="exportDropdown" class="export-dropdown" style="display:none;position:absolute;top:100%;right:0;background:rgba(20,20,40,0.95);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:0.5rem 0;z-index:100;min-width:160px;margin-top:0.3rem;">
                                <button onclick="exportPlanet('png')" style="display:block;width:100%;padding:0.5rem 1rem;background:none;border:none;color:white;text-align:left;cursor:pointer;font-family:inherit;font-size:0.85rem;"><i class="far fa-image"></i> PNG Image</button>
                                <button onclick="exportPlanet('gif')" style="display:block;width:100%;padding:0.5rem 1rem;background:none;border:none;color:white;text-align:left;cursor:pointer;font-family:inherit;font-size:0.85rem;"><i class="fas fa-film"></i> Animated GIF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <canvas id="view-planet-canvas" width="400" height="400" style="width:100%;max-width:400px;display:block;margin:0 auto 1rem;"></canvas>
                <h1 style="font-family:'Orbitron',sans-serif;color:#00f7ff;text-align:center;">${planet.name}</h1>
                <p style="color:#b0b0ff;text-align:center;">Created by <a href="explorer.php?id=${planet.user_id}" style="color:#ff00c8;text-decoration:none;">${planet.username}</a> | ${planet.like_count} likes</p>
                ${planet.backstory ? `<div style="margin-top:1.5rem;line-height:1.8;background:rgba(255,255,255,0.03);padding:1.5rem;border-radius:12px;"><strong style="color:#00f7ff;">Backstory:</strong><br>${planet.backstory}</div>` : ''}
                <div style="text-align:center;margin-top:1rem;">
                    <button onclick="toggleLike(${planet.id})" class="btn-like" style="background:${planet.liked ? '#ff00c8' : 'rgba(255,255,255,0.1)'};color:white;border:1px solid rgba(255,255,255,0.2);padding:0.6rem 2rem;border-radius:2rem;cursor:pointer;font-family:'Orbitron',sans-serif;"><i class="${planet.liked ? 'fas' : 'far'} fa-heart"></i> ${planet.liked ? 'Liked' : 'Like'}</button>
                </div>
            `;

            const viewCanvas = document.getElementById('view-planet-canvas');
            if (viewCanvas) {
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
                let viewAngle = 0;
                let viewLastTime = 0;
                // Animates the single-planet detail view.
                function animateView(timestamp) {
                    if (viewLastTime === 0) {
                        viewLastTime = timestamp;
                        requestAnimationFrame(animateView);
                        return;
                    }
                    const delta = (timestamp - viewLastTime) / 1000;
                    viewLastTime = timestamp;
                    const speedRadPerSec = ((planet.rotation_speed || 0) / 100) * Math.PI * 2;
                    drawPlanetOnCtx(viewCanvas.getContext('2d'), 400, 400, config, viewAngle);
                    viewAngle += speedRadPerSec * delta;
                    requestAnimationFrame(animateView);
                }
                requestAnimationFrame(animateView);
            }
        });
}

// Shows or hides the export menu.
function toggleExportDropdown(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('exportDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', () => {
    const dropdown = document.getElementById('exportDropdown');
    if (dropdown) dropdown.style.display = 'none';
});

// Displays export progress while a GIF is being captured.
function showProgressBar() {
    document.getElementById('exportDropdown').style.display = 'none';
    const container = document.getElementById('singlePlanet');
    const progressHTML = `
        <div id="gifProgress" style="margin:0 0 1rem 0;text-align:center;">
            <p style="color:#00f7ff;font-family:'Orbitron',sans-serif;margin-bottom:0.5rem;">Capturing rotation...</p>
            <div style="background:rgba(255,255,255,0.1);border-radius:10px;height:10px;overflow:hidden;max-width:400px;margin:0 auto;">
                <div id="gifProgressBar" style="background:linear-gradient(90deg,#7b2fff,#ff00c8);height:100%;width:0%;transition:width 0.1s;border-radius:10px;"></div>
            </div>
            <p id="gifProgressText" style="color:#b0b0ff;font-size:0.8rem;margin-top:0.3rem;">0%</p>
        </div>
    `;
    container.insertAdjacentHTML('afterbegin', progressHTML);
}

// Removes the export progress UI.
function hideProgressBar() {
    const progress = document.getElementById('gifProgress');
    if (progress) progress.remove();
}

// Exports the current planet as PNG or GIF.
function exportPlanet(format) {
    const canvas = document.getElementById('view-planet-canvas');
    if (!canvas) return;
    
    if (format === 'png') {
        document.getElementById('exportDropdown').style.display = 'none';
        const link = document.createElement('a');
        const cleanName = (currentPlanet && currentPlanet.name) 
            ? currentPlanet.name.replace(/\s+/g, '-').toLowerCase() 
            : 'cosmic-planet';
        link.download = cleanName + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        showToast('Planet saved as PNG!', 'success');
    } else if (format === 'gif') {
        const rotationSpeed = (currentPlanet && currentPlanet.rotation_speed) || 5;
        if (rotationSpeed === 0) {
            showToast('This planet has no rotation. Cannot create GIF.', 'error');
            document.getElementById('exportDropdown').style.display = 'none';
            return;
        }
        
        const fps = 12;
        const rotationTime = 100 / rotationSpeed;
        const totalFrames = Math.max(24, Math.round(rotationTime * fps));
        const frameDelay = Math.round(1000 / fps);
        
        showProgressBar();
        
        const gif = new GIF({
            workers: 2,
            quality: 10,
            width: 400,
            height: 400,
            workerScript: '/CosmicDiary/node_modules/gif.js/dist/gif.worker.js'
        });
        
        let frame = 0;
        const progressBar = document.getElementById('gifProgressBar');
        const progressText = document.getElementById('gifProgressText');
        
        // Captures one GIF frame during export.
        function captureFrame() {
            if (frame >= totalFrames) {
                if (progressText) progressText.textContent = 'Encoding GIF...';
                gif.on('finished', function(blob) {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const cleanName = (currentPlanet && currentPlanet.name) 
                        ? currentPlanet.name.replace(/\s+/g, '-').toLowerCase() 
                        : 'cosmic-planet';
                    link.download = cleanName + '-loop.gif';
                    link.href = url;
                    link.click();
                    hideProgressBar();
                    showToast('Perfect loop GIF saved!', 'success');
                });
                gif.render();
                return;
            }
            
            gif.addFrame(canvas, { copy: true, delay: frameDelay });
            frame++;
            
            const percent = Math.round((frame / totalFrames) * 100);
            if (progressBar) progressBar.style.width = percent + '%';
            if (progressText) progressText.textContent = percent + '%';
            
            setTimeout(captureFrame, frameDelay);
        }
        
        captureFrame();
    }
}