var board = document.getElementById('board');
var bench = document.getElementById('bench');
var shopCards = document.getElementById('shopCards');
var logBox = document.getElementById('combatLog');
var playersList = document.getElementById('playersList');
var synergyPanel = document.getElementById('synergyPanel');

var playerHpEl = document.getElementById('playerHp');
var levelEl = document.getElementById('playerLevel');
var xpEl = document.getElementById('playerXp');
var goldEl = document.getElementById('playerGold');
var roundEl = document.getElementById('round');
var phaseLabel = document.getElementById('phaseLabel');
var enemyNameEl = document.getElementById('enemyName');
var prepTimerEl = document.getElementById('prepTimer');
var timerWrap = document.getElementById('timerWrap');

var unitsData = window.INITIAL_UNITS || [];
var boardUnits = [];
var benchUnits = [];
var shop = [];
var combatUnits = [];
var draggedId = null;
var round = 1;
var phase = 'prep';
var fighting = false;
var player = { name: window.PLAYER_NAME || 'Joueur', hp: 100, gold: 10, level: 1, xp: 0, streak: 0 };
var bots = [];
var currentBotIndex = 0;
var xpNeeded = [0, 2, 6, 10, 20, 36, 56, 80, 100];
var fightTimer = null;
var prepDuration = 30;
var prepRemaining = prepDuration;
var prepInterval = null;
var audioCtx = null;
var soundEnabled = true;
var musicInterval = null;

// UI vente (bouton + sélection d'unités)
var sellBtn = document.getElementById('sellSelected');
var sellHint = document.getElementById('sellHint');
var selectedUid = null;

// Contexte ennemi (IA / scaling / synergies)
var enemyCtx = { bonus: null, power: 1 };

// Combat : métriques pour permettre des déplacements "libres" (coordonnées flottantes)
var gridMetrics = null;
var fightTickMs = 90;

// Affichage des matchups (leaderboard)
var playerOpponentName = null;

// Spectateur
var spectateModal = document.getElementById('spectateModal');
var spectateTitle = document.getElementById('spectateTitle');
var closeSpectateBtn = document.getElementById('closeSpectate');
var spectating = false;
var spectateTimer = null;
var savedState = null;

var botProfiles = [
    { name: 'Aatrox Noirfer', icon: '🩸' },
    { name: 'Lux Solaris', icon: '✨' },
    { name: 'Kha Zirk', icon: '🦂' },
    { name: 'Darius Acier', icon: '🪓' },
    { name: 'Vex Brume', icon: '🌫' },
    { name: 'Karma Aube', icon: '🌅' },
    { name: 'Rengar Cendre', icon: '🐾' },
    { name: 'Syndra Noctis', icon: '🌙' }
];
for (var i = 0; i < botProfiles.length; i++) {
    bots.push({
        name: botProfiles[i].name,
        icon: botProfiles[i].icon,
        hp: 100,
        gold: 10,
        level: 1,
        xp: 0,
        streak: 0,
        dead: false,
        army: [],
        opponentName: null
    });
}

function ensureAudioReady() {
    if (!soundEnabled) return false;
    if (!audioCtx) {
        var AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return false;
        audioCtx = new AudioContextClass();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return true;
}

function playTone(freq, duration, volume, type) {
    if (!ensureAudioReady()) return;
    var now = audioCtx.currentTime;
    var osc = audioCtx.createOscillator();
    var gain = audioCtx.createGain();
    osc.type = type || 'triangle';
    osc.frequency.setValueAtTime(freq, now);
    gain.gain.setValueAtTime(volume || 0.04, now);
    gain.gain.exponentialRampToValueAtTime(0.0001, now + duration);
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    osc.start(now);
    osc.stop(now + duration + 0.02);
}

function playToneAt(freq, duration, volume, type, delay) {
    if (!ensureAudioReady()) return;
    var now = audioCtx.currentTime + (delay || 0);
    var osc = audioCtx.createOscillator();
    var gain = audioCtx.createGain();
    osc.type = type || 'triangle';
    osc.frequency.setValueAtTime(freq, now);
    gain.gain.setValueAtTime(volume || 0.03, now);
    gain.gain.exponentialRampToValueAtTime(0.0001, now + duration);
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    osc.start(now);
    osc.stop(now + duration + 0.02);
}

function startBackgroundMusic() {
    if (!soundEnabled || musicInterval) return;
    if (!ensureAudioReady()) return;
    var pad = [220, 247, 196, 174];
    var bell = [440, 494, 392, 349];
    var idx = 0;
    function playBar() {
        if (!soundEnabled) return;
        var root = pad[idx % pad.length];
        var lead = bell[idx % bell.length];
        playToneAt(root, 1.5, 0.018, 'sine', 0);
        playToneAt(root * 1.5, 1.1, 0.012, 'triangle', 0.25);
        playToneAt(lead, 0.22, 0.018, 'triangle', 0.45);
        playToneAt(lead * 1.122, 0.22, 0.014, 'triangle', 0.85);
        playToneAt(lead * 1.26, 0.26, 0.014, 'triangle', 1.25);
        idx++;
    }
    playBar();
    musicInterval = setInterval(playBar, 1800);
}

function stopBackgroundMusic() {
    if (musicInterval) {
        clearInterval(musicInterval);
        musicInterval = null;
    }
}

function playSfx(name) {
    if (!soundEnabled) return;
    if (name === 'buy') { playTone(660, 0.08, 0.05, 'triangle'); playTone(880, 0.1, 0.03, 'triangle'); }
    else if (name === 'reroll') { playTone(320, 0.08, 0.04, 'square'); playTone(280, 0.07, 0.03, 'square'); }
    else if (name === 'attack') { playTone(220, 0.05, 0.03, 'sawtooth'); }
    else if (name === 'melee') { playTone(140, 0.06, 0.04, 'sawtooth'); playTone(220, 0.04, 0.03, 'square'); }
    else if (name === 'cast') { playTone(520, 0.05, 0.03, 'triangle'); playTone(780, 0.07, 0.02, 'sine'); }
    else if (name === 'hit') { playTone(180, 0.06, 0.03, 'square'); }
    else if (name === 'kill') { playTone(190, 0.08, 0.04, 'square'); playTone(120, 0.12, 0.04, 'triangle'); }
    else if (name === 'fusion2') {
        // Chime "à la TFT / Clash Royale" (léger et satisfaisant)
        playToneAt(740, 0.06, 0.03, 'triangle', 0.00);
        playToneAt(988, 0.08, 0.032, 'triangle', 0.06);
        playToneAt(1318, 0.10, 0.028, 'sine', 0.12);
    }
    else if (name === 'fusion3') {
        playToneAt(740, 0.06, 0.03, 'triangle', 0.00);
        playToneAt(988, 0.08, 0.032, 'triangle', 0.05);
        playToneAt(1318, 0.10, 0.03, 'triangle', 0.10);
        playToneAt(1760, 0.14, 0.028, 'sine', 0.16);
    }
    else if (name === 'level') { playTone(523, 0.09, 0.05, 'triangle'); playTone(659, 0.09, 0.04, 'triangle'); playTone(783, 0.12, 0.04, 'triangle'); }
    else if (name === 'win') { playTone(440, 0.1, 0.05, 'triangle'); playTone(587, 0.1, 0.04, 'triangle'); playTone(784, 0.16, 0.04, 'triangle'); }
    else if (name === 'lose') { playTone(280, 0.1, 0.05, 'sawtooth'); playTone(220, 0.18, 0.04, 'sawtooth'); }
}

function playFusionSfx(stars) {
    if (!ensureAudioReady()) return;
    if (stars >= 3) playSfx('fusion3');
    else playSfx('fusion2');
}

function safeNumber(v, fallback) {
    var n = Number(v);
    return isNaN(n) ? fallback : n;
}

function toUnitData(p) {
    return {
        baseId: safeNumber(p.id, 0),
        name: p.nom || 'Unité',
        cost: safeNumber(p.cout, 1),
        atk: safeNumber(p.attaque, 10),
        def: safeNumber(p.defense, 3),
        maxHp: safeNumber(p.pv, 100),
        speed: safeNumber(p.vitesse, 1),
        range: safeNumber(p.portee, 1),
        origin: p.origine || 'Libre',
        classe: p.classe || 'Combattant',
        icon: 'uploads/' + (p.icone || 'default.png')
    };
}

function costClass(cost) {
    return 'cost-border-' + Math.min(5, Math.max(1, cost));
}

function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

function computeGridMetrics() {
    var cells = document.querySelectorAll('.cell');
    if (!cells || !cells.length) return null;
    var br = board.getBoundingClientRect();
    var r0 = cells[0].getBoundingClientRect();
    var r1 = cells[1] ? cells[1].getBoundingClientRect() : r0;
    var r5 = cells[5] ? cells[5].getBoundingClientRect() : r0;
    var cellW = Math.max(1, (r1.left - r0.left) || r0.width);
    var cellH = Math.max(1, (r5.top - r0.top) || r0.height);
    var offsetX = r0.left - br.left;
    var offsetY = r0.top - br.top;
    var tokenSize = Math.min(cellW, cellH) * 0.82;
    return { cellW: cellW, cellH: cellH, offsetX: offsetX, offsetY: offsetY, tokenSize: tokenSize };
}

function cellToPixel(x, y) {
    if (!gridMetrics) gridMetrics = computeGridMetrics();
    var m = gridMetrics;
    if (!m) return { left: 0, top: 0, size: 60 };
    var xx = clamp(x, 0, 4);
    var yy = clamp(y, 0, 3);
    return {
        left: m.offsetX + (xx * m.cellW) + (m.cellW - m.tokenSize) / 2,
        top: m.offsetY + (yy * m.cellH) + (m.cellH - m.tokenSize) / 2,
        size: m.tokenSize
    };
}

function centerPx(u) {
    var s = u.size || (gridMetrics ? gridMetrics.tokenSize : 60);
    return { x: (u.px || 0) + s / 2, y: (u.py || 0) + s / 2 };
}

function isRanged(u) { return (u && u.range) ? u.range > 1.1 : false; }

function effectiveRange(u) {
    // Différence "assumée" : mêlée = contact, distance = vraie zone de tir
    if (!u) return 1;
    return isRanged(u) ? (u.range + 0.9) : 0.85;
}

function randomUnitFromPool(levelOverride) {
    if (unitsData.length === 0) return null;
    var oddsByLevel = {
        1: [1, 0, 0, 0, 0],
        2: [1, 0, 0, 0, 0],
        3: [0.75, 0.25, 0, 0, 0],
        4: [0.55, 0.30, 0.15, 0, 0],
        5: [0.45, 0.33, 0.20, 0.02, 0],
        6: [0.30, 0.40, 0.25, 0.05, 0],
        7: [0.19, 0.35, 0.30, 0.15, 0.01],
        8: [0.14, 0.25, 0.35, 0.20, 0.06]
    };
    var lvl = levelOverride || player.level;
    var levelOdds = oddsByLevel[lvl] || oddsByLevel[8];
    var roll = Math.random();
    var acc = 0;
    var targetCost = 1;
    for (var i = 0; i < levelOdds.length; i++) {
        acc += levelOdds[i];
        if (roll <= acc) {
            targetCost = i + 1;
            break;
        }
    }
    var pool = unitsData.filter(function(p) { return safeNumber(p.cout, 1) === targetCost; });
    if (!pool.length) pool = unitsData.filter(function(p) { return safeNumber(p.cout, 1) <= targetCost; });
    if (!pool.length) pool = unitsData;
    return toUnitData(pool[Math.floor(Math.random() * pool.length)]);
}

function makeOwnedUnit(data, stars) {
    return {
        uid: 'u_' + Date.now() + '_' + Math.random(),
        baseId: data.baseId,
        name: data.name,
        cost: data.cost,
        atk: data.atk,
        def: data.def,
        maxHp: data.maxHp,
        speed: data.speed,
        range: data.range,
        origin: data.origin,
        classe: data.classe,
        icon: data.icon,
        stars: stars || 1,
        zone: 'bench',
        x: null,
        y: null
    };
}

function starText(n) {
    var s = '';
    for (var i = 0; i < n; i++) s += '★';
    return s;
}

function getStarMultiplier(stars) {
    return stars === 2 ? 1.8 : stars === 3 ? 3.2 : 1;
}

function getSynergyBonusFromList(list) {
    var counts = {};
    (list || []).forEach(function(u) {
        counts[u.origin] = (counts[u.origin] || 0) + 1;
        counts[u.classe] = (counts[u.classe] || 0) + 1;
    });
    var bonus = { atk: 0, hp: 0, def: 0, speed: 0, active: [] };
    Object.keys(counts).forEach(function(k) {
        var c = counts[k];
        if (c >= 2) { bonus.atk += 4; bonus.hp += 20; bonus.active.push(k + ' x' + c); }
        if (c >= 3) { bonus.atk += 8; bonus.def += 3; }
        if (c >= 4) { bonus.hp += 35; bonus.speed += 0.25; }
    });
    return bonus;
}

function getSynergyBonus() {
    return getSynergyBonusFromList(boardUnits);
}

function effectiveStats(u) {
    var mult = getStarMultiplier(u.stars);
    var b = getSynergyBonus();
    return {
        atk: Math.round(u.atk * mult + b.atk),
        def: Math.round(u.def * mult + b.def),
        maxHp: Math.round(u.maxHp * mult + b.hp),
        speed: u.speed + b.speed,
        range: u.range
    };
}

function baseStats(u) {
    var mult = getStarMultiplier(u.stars);
    return {
        atk: Math.round(u.atk * mult),
        def: Math.round(u.def * mult),
        maxHp: Math.round(u.maxHp * mult),
        speed: u.speed,
        range: u.range
    };
}

function enemyEffectiveStats(u) {
    var mult = getStarMultiplier(u.stars);
    var b = enemyCtx && enemyCtx.bonus ? enemyCtx.bonus : { atk: 0, hp: 0, def: 0, speed: 0 };
    var p = enemyCtx && enemyCtx.power ? enemyCtx.power : 1;
    return {
        atk: Math.round((u.atk * mult + b.atk) * p),
        def: Math.round((u.def * mult + b.def) * p),
        maxHp: Math.round((u.maxHp * mult + b.hp) * p),
        speed: (u.speed + b.speed) * (0.96 + Math.min(0.2, (p - 1) * 0.15)),
        range: u.range
    };
}

function createBoard() {
    board.innerHTML = '';
    for (var y = 0; y < 4; y++) {
        for (var x = 0; x < 5; x++) {
            var cell = document.createElement('div');
            cell.className = 'cell ' + (y < 2 ? 'enemy-zone' : 'player-zone');
            cell.dataset.x = x;
            cell.dataset.y = y;
            cell.addEventListener('dragover', function(e) { if (phase === 'prep') e.preventDefault(); });
            cell.addEventListener('drop', dropOnBoard);
            board.appendChild(cell);
        }
    }
}

function renderAll() {
    renderBoardUnits();
    renderBench();
    renderShop();
    renderHud();
    renderPlayers();
    renderSynergies();
    bindUnitActions();
    applySelectionUi();
}

function unitHtml(u, extraClass) {
    return '<div class="owned-unit team-player ' + costClass(u.cost) + ' ' + (extraClass || '') + '" draggable="' + (phase === 'prep') + '" data-uid="' + u.uid + '">' +
        '<img src="' + u.icon + '" alt="">' +
        '<span class="unit-name">' + u.name + '</span>' +
        '<span class="stars">' + starText(u.stars) + '</span>' +
        '</div>';
}

function renderBoardUnits() {
    var cells = document.querySelectorAll('.cell');
    cells.forEach(function(c) { c.innerHTML = ''; });
    boardUnits.forEach(function(u) {
        var cell = cells[u.y * 5 + u.x];
        if (cell) cell.innerHTML = unitHtml(u, 'board-piece');
    });
    bindDraggables();
}

function renderBench() {
    bench.innerHTML = '';
    for (var i = 0; i < 9; i++) {
        var slot = document.createElement('div');
        slot.className = 'bench-slot';
        slot.dataset.index = i;
        slot.addEventListener('dragover', function(e) { if (phase === 'prep') e.preventDefault(); });
        slot.addEventListener('drop', dropOnBench);
        if (benchUnits[i]) slot.innerHTML = unitHtml(benchUnits[i], 'bench-piece');
        bench.appendChild(slot);
    }
    bindDraggables();
}

function renderShop() {
    shopCards.innerHTML = '';
    shop.forEach(function(u, i) {
        var card = document.createElement('button');
        card.className = 'shop-card cost-' + Math.min(5, u.cost);
        card.dataset.index = i;
        card.innerHTML =
            '<img src="' + u.icon + '" alt="">' +
            '<div class="shop-card-info">' +
            '<strong>' + u.name + '</strong>' +
            '<span class="shop-cost">🪙 ' + u.cost + '</span>' +
            '<small>' + u.origin + ' · ' + u.classe + '</small>' +
            '</div>';
        card.addEventListener('click', buyFromShop);
        shopCards.appendChild(card);
    });
}

function renderHud() {
    playerHpEl.innerHTML = player.hp;
    levelEl.innerHTML = player.level;
    xpEl.innerHTML = player.level >= 8 ? 'MAX' : player.xp + '/' + xpNeeded[player.level];
    goldEl.innerHTML = player.gold;
    roundEl.innerHTML = round;
    phaseLabel.innerHTML = phase === 'prep' ? 'Préparation' : (phase === 'spectate' ? 'Spectateur' : 'Combat');
    if (prepTimerEl) {
        prepTimerEl.innerHTML = phase === 'prep' ? Math.max(0, prepRemaining) + 's' : '--';
    }
    if (timerWrap) {
        timerWrap.className = 'hud-stat timer-stat' + (phase === 'prep' && prepRemaining <= 5 ? ' urgent' : '');
    }
    var enemy = getCurrentBot(false);
    enemyNameEl.innerHTML = enemy ? enemy.name : 'Victoire';
    var fightBtn = document.getElementById('startFight');
    if (fightBtn) fightBtn.disabled = phase !== 'prep' || fighting;
}

function renderPlayers() {
    playersList.innerHTML = '';
    var p = document.createElement('div');
    var pHot = player.streak >= 4;
    p.className = 'player-line main-player' + (player.hp <= 0 ? ' dead' : '') + (pHot ? ' hot-streak' : '');
    var pXpMax = player.level >= 8 ? 1 : Math.max(1, xpNeeded[player.level]);
    var pXpPct = player.level >= 8 ? 100 : Math.max(0, Math.min(100, Math.round((player.xp / pXpMax) * 100)));
    p.innerHTML =
        '<strong>Toi</strong><span>' + player.hp + ' PV</span>' +
        '<small>Niv. ' + player.level + ' · ' + player.gold + ' PO · Série ' + player.streak + (playerOpponentName ? (' · vs ' + playerOpponentName) : '') + '</small>' +
        '<div class="hp-bar-wrap"><div class="hp-bar-fill" style="width:' + player.hp + '%"></div></div>' +
        '<div class="xp-bar-wrap"><div class="xp-bar-fill" style="width:' + pXpPct + '%"></div></div>';
    playersList.appendChild(p);

    bots.forEach(function(b, i) {
        var d = document.createElement('div');
        var hot = b.streak >= 4;
        d.className = 'player-line' + (b.dead ? ' dead' : '') + (i === currentBotIndex ? ' current' : '') + (hot ? ' hot-streak' : '');
        var xpMax = b.level >= 8 ? 1 : Math.max(1, xpNeeded[b.level]);
        var xpPct = b.level >= 8 ? 100 : Math.max(0, Math.min(100, Math.round((b.xp / xpMax) * 100)));
        var canSpectate = (phase === 'prep') && !fighting && !!b.opponentName && b.opponentName !== 'Toi' && !b.dead && b.hp > 0;
        var encodedName = encodeURIComponent(b.name);
        d.innerHTML =
            '<strong><span class="bot-icon">' + (b.icon || '🤖') + '</span> ' + b.name + '</strong><span>' + b.hp + ' PV</span>' +
            '<small>Niv. ' + b.level + ' · Série ' + b.streak + (b.opponentName ? (' · vs ' + b.opponentName) : '') + '</small>' +
            '<div class="hp-bar-wrap"><div class="hp-bar-fill" style="width:' + b.hp + '%"></div></div>' +
            '<div class="xp-bar-wrap"><div class="xp-bar-fill" style="width:' + xpPct + '%"></div></div>' +
            '<button class="spectate-btn" type="button" ' + (canSpectate ? '' : 'disabled') + ' data-spectate="' + encodedName + '" title="Spectate">👁</button>';
        playersList.appendChild(d);
    });
}

function shuffleInPlace(arr) {
    for (var i = arr.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = arr[i];
        arr[i] = arr[j];
        arr[j] = tmp;
    }
    return arr;
}

function botStrength(bot) {
    // Force estimée: niveau + composition (coût * étoiles) + un peu d'aléatoire
    var s = bot.level * 12 + (bot.gold || 0) * 0.6;
    if (bot.army && bot.army.length) {
        for (var i = 0; i < bot.army.length; i++) {
            var u = bot.army[i];
            s += (u.cost || 1) * 8 * (u.stars || 1);
        }
    } else {
        // fallback si pas d'armée stockée
        s += bot.level * 10;
    }
    s += Math.random() * 18;
    return s;
}

function applyStreak(obj, win) {
    if (win) obj.streak = obj.streak >= 0 ? obj.streak + 1 : 1;
    else obj.streak = obj.streak <= 0 ? obj.streak - 1 : -1;
}

function botLevelUpCheck(bot) {
    while (bot.level < 8 && bot.xp >= xpNeeded[bot.level]) {
        bot.xp -= xpNeeded[bot.level];
        bot.level++;
    }
}

function prepareRoundMatchups(playerBot) {
    // Reset
    playerOpponentName = null;
    bots.forEach(function(b) { b.opponentName = null; b.ghostSource = null; });

    if (playerBot) {
        playerOpponentName = playerBot.name;
        playerBot.opponentName = 'Toi';
    }

    // Les bots doivent "jouer en même temps" : on les associe par paires (hors bot du joueur)
    var alive = bots.filter(function(b) { return !b.dead && b.hp > 0; });
    var others = alive.filter(function(b) { return !playerBot || b !== playerBot; });
    shuffleInPlace(others);
    for (var i = 0; i + 1 < others.length; i += 2) {
        others[i].opponentName = others[i + 1].name;
        others[i + 1].opponentName = others[i].name;
    }

    // Si nombre impair : le dernier affronte un "Fantôme" (comme TFT)
    if (others.length % 2 === 1) {
        var lonely = others[others.length - 1];
        var pool = others.slice(0, -1);
        var src = pool.length ? pool[Math.floor(Math.random() * pool.length)] : null;
        lonely.opponentName = src ? ('Fantôme') : 'Fantôme';
        lonely.ghostSource = src ? src.name : null;
    }
}

function resolveBotBattles(playerBot) {
    // Résout les fights bots vs bots (simulés) et met à jour PV, streak, xp, level
    var alive = bots.filter(function(b) { return !b.dead && b.hp > 0; });
    var others = alive.filter(function(b) { return !playerBot || b !== playerBot; });
    var byName = {};
    others.forEach(function(b) { byName[b.name] = b; });

    var done = {};
    var summaries = [];

    function dmgFor(winner) {
        return 3 + Math.floor(round / 2) + Math.floor(winner.level / 2) + Math.min(3, (winner.army || []).length);
    }

    function giveBotRewards(winner, loser) {
        winner.gold = (winner.gold || 0) + 3;
        loser.gold = (loser.gold || 0) + 3;
        winner.xp = (winner.xp || 0) + 2;
        loser.xp = (loser.xp || 0) + 1;
        botLevelUpCheck(winner);
        botLevelUpCheck(loser);
    }

    function resolvePair(a, b) {
        botArmy(a);
        botArmy(b);
        var sa = botStrength(a);
        var sb = botStrength(b);
        var winner = sa >= sb ? a : b;
        var loser = winner === a ? b : a;
        var dmg = dmgFor(winner);
        loser.hp = Math.max(0, loser.hp - dmg);
        if (loser.hp <= 0) loser.dead = true;
        applyStreak(winner, true);
        applyStreak(loser, false);
        giveBotRewards(winner, loser);
        summaries.push(winner.name + ' bat ' + loser.name + ' (-' + dmg + ' PV)');
    }

    function resolveGhostFight(bot) {
        // Fantôme inspiré d'un autre bot (sinon fantôme neutre). Le fantôme ne perd jamais de PV.
        var src = bot.ghostSource ? byName[bot.ghostSource] : null;
        var ghost = {
            name: 'Fantôme',
            level: src ? src.level : bot.level,
            gold: src ? src.gold : 10,
            xp: 0,
            streak: 0,
            hp: 999,
            dead: false,
            army: []
        };
        botArmy(bot);
        botArmy(ghost);
        var sb = botStrength(bot);
        var sg = botStrength(ghost);
        var botWins = sb >= sg;
        if (botWins) {
            applyStreak(bot, true);
            bot.gold = (bot.gold || 0) + 3;
            bot.xp = (bot.xp || 0) + 2;
            botLevelUpCheck(bot);
            summaries.push(bot.name + ' bat Fantôme');
        } else {
            var dmg = dmgFor(ghost);
            bot.hp = Math.max(0, bot.hp - dmg);
            if (bot.hp <= 0) bot.dead = true;
            applyStreak(bot, false);
            bot.gold = (bot.gold || 0) + 3;
            bot.xp = (bot.xp || 0) + 1;
            botLevelUpCheck(bot);
            summaries.push('Fantôme bat ' + bot.name + ' (-' + dmg + ' PV)');
        }
    }

    // Résolution fidèle aux matchups affichés (tous les bots ont un fight, y compris via Fantôme)
    others.forEach(function(a) {
        if (done[a.name]) return;
        var opp = a.opponentName;
        if (!opp || opp === 'Toi') {
            // fallback : fight fantôme
            done[a.name] = true;
            resolveGhostFight(a);
            return;
        }
        if (opp === 'Fantôme') {
            done[a.name] = true;
            resolveGhostFight(a);
            return;
        }
        var b = byName[opp];
        if (!b || b.dead || b.hp <= 0) {
            done[a.name] = true;
            resolveGhostFight(a);
            return;
        }
        // éviter double résolution
        done[a.name] = true;
        done[b.name] = true;
        resolvePair(a, b);
    });

    if (!summaries.length) return '';
    return 'Bots : ' + summaries.slice(0, 2).join(' · ') + (summaries.length > 2 ? ' …' : '');
}

function renderSynergies() {
    var counts = {};
    boardUnits.forEach(function(u) {
        counts[u.origin] = (counts[u.origin] || 0) + 1;
        counts[u.classe] = (counts[u.classe] || 0) + 1;
    });
    var html = '';
    Object.keys(counts).sort().forEach(function(k) {
        var c = counts[k];
        html += '<div class="synergy' + (c >= 2 ? ' active' : '') + '"><b>' + k + '</b><span>' + c + '/2</span></div>';
    });
    synergyPanel.innerHTML = html || '<div class="synergy"><b>Aucun trait</b><span>0/2</span></div>';
}

function bindDraggables() {
    document.querySelectorAll('.owned-unit').forEach(function(el) {
        el.addEventListener('dragstart', function() {
            if (phase === 'prep') draggedId = this.dataset.uid;
        });
    });
}

function bindUnitActions() {
    document.querySelectorAll('.owned-unit').forEach(function(el) {
        el.addEventListener('dblclick', function(e) {
            e.preventDefault();
            if (phase !== 'prep') return;
            if (sellUnitByUid(this.dataset.uid)) renderAll();
        });

        el.addEventListener('click', function() {
            if (phase !== 'prep') return;
            selectUnit(this.dataset.uid);
        });
    });
}

function applySelectionUi() {
    document.querySelectorAll('.owned-unit.selected').forEach(function(el) { el.classList.remove('selected'); });
    if (!selectedUid) { updateSellUi(); return; }
    var el = document.querySelector('[data-uid="' + selectedUid + '"]');
    if (el) el.classList.add('selected');
    updateSellUi();
}

function sellValueFor(u) {
    if (!u) return 0;
    return Math.max(1, Math.floor(u.cost * (u.stars === 3 ? 3 : u.stars === 2 ? 2 : 1)));
}

function updateSellUi() {
    if (!sellBtn) return;
    if (!selectedUid) {
        sellBtn.disabled = true;
        if (sellHint) sellHint.textContent = 'Sélectionne une unité';
        return;
    }
    var f = findOwned(selectedUid);
    var val = f && f.unit ? sellValueFor(f.unit) : 0;
    sellBtn.disabled = !val || phase !== 'prep';
    if (sellHint) sellHint.textContent = val ? ('+' + val + ' PO') : '—';
}

function selectUnit(uid) {
    selectedUid = uid || null;
    applySelectionUi();
}

function findOwned(uid) {
    for (var i = 0; i < boardUnits.length; i++) {
        if (boardUnits[i].uid === uid) return { unit: boardUnits[i], place: 'board', index: i };
    }
    for (var j = 0; j < benchUnits.length; j++) {
        if (benchUnits[j] && benchUnits[j].uid === uid) return { unit: benchUnits[j], place: 'bench', index: j };
    }
    return null;
}

function removeOwned(f) {
    if (!f) return;
    if (f.place === 'board') boardUnits.splice(f.index, 1);
    else benchUnits[f.index] = null;
}

function occupied(x, y) {
    return boardUnits.find(function(u) { return u.x === x && u.y === y; });
}

function firstFreeBenchSlot() {
    for (var i = 0; i < 9; i++) if (!benchUnits[i]) return i;
    return -1;
}

function dropOnBoard(e) {
    e.preventDefault();
    if (phase !== 'prep' || !draggedId) return;
    var x = Number(this.dataset.x);
    var y = Number(this.dataset.y);
    if (y < 2) { log('Place tes unités sur les 2 lignes du bas.'); return; }
    var f = findOwned(draggedId);
    if (!f) return;
    if (f.place !== 'board' && boardUnits.length >= player.level) {
        log('Limite : niveau ' + player.level + ' = ' + player.level + ' unité(s) max.');
        return;
    }
    var target = occupied(x, y);
    if (target && target.uid !== draggedId) {
        if (f.place === 'bench') {
            var free = firstFreeBenchSlot();
            if (free === -1) { log('Case occupée et banc plein.'); return; }
            removeUnitByUid(target.uid);
            target.zone = 'bench';
            target.x = null;
            target.y = null;
            benchUnits[free] = target;
        } else {
            target.x = f.unit.x;
            target.y = f.unit.y;
        }
    }
    removeOwned(f);
    f.unit.zone = 'board';
    f.unit.x = x;
    f.unit.y = y;
    boardUnits.push(f.unit);
    draggedId = null;
    renderAll();
}

function dropOnBench(e) {
    e.preventDefault();
    if (phase !== 'prep' || !draggedId) return;
    var idx = Number(this.dataset.index);
    var f = findOwned(draggedId);
    if (!f) return;
    var occupant = benchUnits[idx];
    if (occupant && occupant.uid !== draggedId) {
        if (f.place === 'board') {
            var oldX = f.unit.x;
            var oldY = f.unit.y;
            benchUnits[idx] = f.unit;
            removeOwned(f);
            f.unit.zone = 'bench';
            f.unit.x = null;
            f.unit.y = null;
            occupant.zone = 'board';
            occupant.x = oldX;
            occupant.y = oldY;
            boardUnits.push(occupant);
        } else {
            benchUnits[f.index] = occupant;
        }
    } else {
        removeOwned(f);
    }
    f.unit.zone = 'bench';
    f.unit.x = null;
    f.unit.y = null;
    benchUnits[idx] = f.unit;
    draggedId = null;
    renderAll();
}

function fillShop() {
    shop = [];
    for (var i = 0; i < 5; i++) {
        var u = randomUnitFromPool();
        if (u) shop.push(u);
    }
    renderShop();
}

function buyFromShop() {
    if (phase !== 'prep') return;
    var idx = Number(this.dataset.index);
    var data = shop[idx];
    if (!data) return;
    if (player.gold < data.cost) { log('Pas assez de PO.'); pulse(goldEl); return; }
    var slot = firstFreeBenchSlot();
    if (slot === -1) { log('Banc plein — vends ou place des unités.'); return; }
    player.gold -= data.cost;
    playSfx('buy');
    benchUnits[slot] = makeOwnedUnit(data, 1);
    shop[idx] = randomUnitFromPool();
    checkAllFusions();
    log(data.name + ' recruté !');
    renderAll();
}

function allOwnedUnits() {
    return boardUnits.concat(benchUnits.filter(Boolean));
}

function removeUnitByUid(uid) {
    for (var i = boardUnits.length - 1; i >= 0; i--) {
        if (boardUnits[i].uid === uid) boardUnits.splice(i, 1);
    }
    for (var j = 0; j < benchUnits.length; j++) {
        if (benchUnits[j] && benchUnits[j].uid === uid) benchUnits[j] = null;
    }
}

function checkAllFusions() {
    var changed = true;
    while (changed) {
        changed = false;
        var owned = allOwnedUnits();
        for (var i = 0; i < owned.length; i++) {
            var baseId = owned[i].baseId;
            var stars = owned[i].stars;
            if (stars >= 3) continue;
            var group = owned.filter(function(u) { return u.baseId === baseId && u.stars === stars; });
            if (group.length >= 2) {
                fuseUnits(group.slice(0, 2));
                changed = true;
                break;
            }
        }
    }
}

function fuseUnits(group) {
    var keep = group[0];
    var found = findOwned(keep.uid);
    var upgraded = Object.assign({}, keep);
    upgraded.uid = 'u_' + Date.now() + '_' + Math.random();
    upgraded.stars = keep.stars + 1;
    removeUnitByUid(group[0].uid);
    removeUnitByUid(group[1].uid);
    if (found && found.place === 'board') {
        upgraded.zone = 'board';
        upgraded.x = keep.x;
        upgraded.y = keep.y;
        boardUnits.push(upgraded);
    } else {
        var slot = found ? found.index : firstFreeBenchSlot();
        upgraded.zone = 'bench';
        upgraded.x = null;
        upgraded.y = null;
        benchUnits[slot < 0 ? 0 : slot] = upgraded;
    }
    log(upgraded.name + ' fusionne (2 copies) en ' + upgraded.stars + '★ !');
    createBurst(upgraded);
    playFusionSfx(upgraded.stars);
}

function createBurst(unit) {
    setTimeout(function() {
        var el = document.querySelector('[data-uid="' + unit.uid + '"]');
        if (el) {
            el.classList.add('fused');
            setTimeout(function() { el.classList.remove('fused'); }, 800);
        }
    }, 50);
}

function log(msg) { logBox.innerHTML = msg; }

function pulse(el) {
    el.classList.add('pulse');
    setTimeout(function() { el.classList.remove('pulse'); }, 400);
}

function sellUnitByUid(uid) {
    var found = findOwned(uid);
    if (!found || !found.unit) return false;
    var gain = Math.max(1, Math.floor(found.unit.cost * (found.unit.stars === 3 ? 3 : found.unit.stars === 2 ? 2 : 1)));
    removeOwned(found);
    player.gold += gain;
    playTone(520, 0.06, 0.04, 'triangle');
    log(found.unit.name + ' vendu : +' + gain + ' PO.');
    return true;
}

function buyXp() {
    if (phase !== 'prep') return;
    if (player.level >= 8) { log('Niveau maximum atteint.'); return; }
    if (player.gold < 4) { log('Pas assez de PO (4 requis).'); return; }
    player.gold -= 4;
    player.xp += 4;
    levelUpCheck();
    renderAll();
}

function levelUpCheck() {
    while (player.level < 8 && player.xp >= xpNeeded[player.level]) {
        player.xp -= xpNeeded[player.level];
        player.level++;
        playSfx('level');
        log('Niveau ' + player.level + ' ! Plus de places sur le plateau.');
    }
}

function rerollShop() {
    if (phase !== 'prep') return;
    if (player.gold < 2) { log('Pas assez de PO pour reroll (2 requis).'); return; }
    player.gold -= 2;
    playSfx('reroll');
    fillShop();
    log('Boutique rafraîchie.');
    renderAll();
}

function getCurrentBot(update) {
    for (var i = 0; i < bots.length; i++) {
        var idx = (currentBotIndex + i) % bots.length;
        if (!bots[idx].dead) {
            if (update !== false) currentBotIndex = idx;
            return bots[idx];
        }
    }
    return null;
}

function botArmy(bot) {
    // IA plus "dure" : plus d'unités plus tôt + meilleures étoiles en late game
    // Le nombre d'unités jouées reste lié au niveau, avec un léger bonus en mid/late
    var count = Math.max(1, Math.min(8, bot.level + (round >= 6 ? 1 : 0) + (round >= 12 ? 1 : 0)));
    var army = [];
    var star2Chance = round >= 4 ? 0.28 : 0.12;
    var star3Chance = round >= 10 ? 0.10 : 0.00;
    if (round >= 15) star3Chance = 0.18;
    for (var i = 0; i < count; i++) {
        var d = randomUnitFromPool(bot.level);
        if (!d) continue;
        var r = Math.random();
        var stars = r < star3Chance ? 3 : (r < star3Chance + star2Chance ? 2 : 1);
        var u = makeOwnedUnit(d, stars);
        u.x = i % 5;
        u.y = Math.floor(i / 5);
        army.push(u);
    }
    bot.army = army;
    return army;
}

function cellPosition(x, y) {
    var cells = document.querySelectorAll('.cell');
    var r = cells[y * 5 + x].getBoundingClientRect();
    var br = board.getBoundingClientRect();
    var tokenSize = Math.min(r.width, r.height) * 0.82;
    var offset = (Math.min(r.width, r.height) - tokenSize) / 2;
    return { left: r.left - br.left + offset, top: r.top - br.top + offset, size: tokenSize };
}

function clearCombatTokens() {
    document.querySelectorAll('.combat-token, .slash, .projectile, .beam, .impact-ring, .spark').forEach(function(e) { e.remove(); });
}

function statsWithCtx(u, ctx) {
    var mult = getStarMultiplier(u.stars);
    var b = ctx && ctx.bonus ? ctx.bonus : { atk: 0, hp: 0, def: 0, speed: 0 };
    var p = ctx && ctx.power ? ctx.power : 1;
    return {
        atk: Math.round((u.atk * mult + b.atk) * p),
        def: Math.round((u.def * mult + b.def) * p),
        maxHp: Math.round((u.maxHp * mult + b.hp) * p),
        speed: (u.speed + b.speed) * (0.96 + Math.min(0.2, (p - 1) * 0.15)),
        range: u.range
    };
}

function makeCombatTokenWithStats(src, team, x, y, stats) {
    var token = document.createElement('div');
    var cost = src.cost || 1;
    token.className = 'combat-token ' + team + ' cost-' + Math.min(5, Math.max(1, cost));
    token.innerHTML =
        '<img src="' + src.icon + '" alt="">' +
        '<span class="stars">' + starText(src.stars) + '</span>' +
        '<div class="hpbar"><div class="hpfill"></div></div>' +
        '<div class="damage-pop"></div>';
    board.appendChild(token);
    var u = {
        id: src.uid + '_' + Math.random(),
        name: src.name,
        team: team,
        cost: cost,
        x: x,
        y: y,
        atk: stats.atk,
        def: stats.def,
        maxHp: stats.maxHp,
        hp: stats.maxHp,
        range: stats.range,
        speed: stats.speed,
        token: token,
        attackCd: 0,
        px: 0,
        py: 0,
        size: 0
    };
    moveCombatToken(u, x, y);
    updateHp(u);
    return u;
}

function makeCombatToken(src, team, x, y) {
    var stats = team === 'player' ? effectiveStats(src) : enemyEffectiveStats(src);
    return makeCombatTokenWithStats(src, team, x, y, stats);
}

function moveCombatToken(u, x, y) {
    var oldX = u.x;
    var oldY = u.y;
    u.x = clamp(x, 0, 4);
    u.y = clamp(y, 0, 3);
    var p = cellToPixel(u.x, u.y);
    u.px = p.left;
    u.py = p.top;
    u.size = p.size;
    var jitterX = (Math.random() * 6) - 3;
    var jitterY = (Math.random() * 6) - 3;
    u.token.style.left = (p.left + jitterX) + 'px';
    u.token.style.top = (p.top + jitterY) + 'px';
    if (oldX !== undefined && (Math.abs(oldX - u.x) > 0.01 || Math.abs(oldY - u.y) > 0.01)) {
        var trail = document.createElement('div');
        trail.className = 'move-trail ' + u.team;
        trail.style.left = (p.left + p.size * 0.35) + 'px';
        trail.style.top = (p.top + p.size * 0.35) + 'px';
        board.appendChild(trail);
        setTimeout(function() { trail.remove(); }, 220);
    }
}

function updateHp(u) {
    var f = u.token.querySelector('.hpfill');
    if (f) f.style.width = Math.max(0, Math.round((u.hp / u.maxHp) * 100)) + '%';
}

function distance(a, b) {
    var dx = a.x - b.x;
    var dy = a.y - b.y;
    return Math.sqrt(dx * dx + dy * dy);
}
function alive(list) { return list.filter(function(u) { return u.hp > 0; }); }
function nearest(u, targets) { return targets.sort(function(a, b) { return distance(u, a) - distance(u, b); })[0]; }
function pickTarget(u, targets) {
    if (!targets.length) return null;
    var inRange = targets.filter(function(t){ return distance(u, t) <= effectiveRange(u); });
    var preferred = inRange.length ? inRange : targets;
    preferred.sort(function(a, b) {
        if (a.hp !== b.hp) return a.hp - b.hp;
        if (a.range !== b.range) return b.range - a.range;
        return distance(u, a) - distance(u, b);
    });
    if (u.range > 1) {
        var backline = preferred.filter(function(t){ return t.range > 1; });
        if (backline.length) return backline[0];
    }
    return preferred[0];
}

function occupiedCombat(x, y, except) {
    return combatUnits.some(function(u) { return u !== except && u.hp > 0 && u.x === x && u.y === y; });
}

function stepToward(u, t, stepMult) {
    var dx = (t.x - u.x);
    var dy = (t.y - u.y);
    var d = Math.sqrt(dx * dx + dy * dy);
    if (!d) return;
    // Déplacements plus libres (coordonnées flottantes)
    var step = (0.18 + (u.speed * 0.07)) * (stepMult || 1);
    step = clamp(step, 0.14, 0.55);
    moveCombatToken(u, u.x + (dx / d) * step, u.y + (dy / d) * step);
}

function stepAway(u, t) {
    var dx = (u.x - t.x);
    var dy = (u.y - t.y);
    var d = Math.sqrt(dx * dx + dy * dy);
    if (!d) return;
    var step = clamp(0.16 + (u.speed * 0.06), 0.14, 0.40);
    moveCombatToken(u, u.x + (dx / d) * step, u.y + (dy / d) * step);
}

function attack(u, t) {
    var ranged = isRanged(u);
    if (ranged) playSfx('cast'); else playSfx('melee');

    var c1 = centerPx(u);
    var c2 = centerPx(t);

    // --- Variété visuelle : mêlée vs distance + styles selon le coût ---
    if (ranged) {
        // coût élevé = "beam" plus impactant, sinon projectile
        if (u.cost >= 4 && Math.random() > 0.45) {
            var beam = document.createElement('div');
            var dx = (c2.x - c1.x);
            var dy = (c2.y - c1.y);
            var len = Math.max(40, Math.sqrt(dx * dx + dy * dy));
            var ang = Math.atan2(dy, dx) * 180 / Math.PI;
            beam.className = 'beam beam-cost-' + Math.min(5, Math.max(1, u.cost));
            beam.style.left = c1.x + 'px';
            beam.style.top = c1.y + 'px';
            beam.style.width = len + 'px';
            beam.style.transform = 'rotate(' + ang + 'deg)';
            board.appendChild(beam);
            u.token.classList.add('cast-shot');
            setTimeout(function() { beam.remove(); }, 140);
        } else {
            var proj = document.createElement('div');
            var variant = 1 + Math.floor(Math.random() * 3);
            proj.className = 'projectile p-cost-' + Math.min(5, Math.max(1, u.cost)) + ' p-v' + variant;
            proj.style.left = (c1.x - 5) + 'px';
            proj.style.top = (c1.y - 5) + 'px';
            board.appendChild(proj);
            u.token.classList.add('cast-shot');
            setTimeout(function() {
                proj.style.left = (c2.x - 5) + 'px';
                proj.style.top = (c2.y - 5) + 'px';
            }, 10);
            setTimeout(function() { proj.remove(); }, 190);
        }
    } else {
        u.token.classList.add('melee-swing');
        var slash = document.createElement('div');
        var sVar = 1 + Math.floor(Math.random() * 3);
        slash.className = 'slash s-cost-' + Math.min(5, Math.max(1, u.cost)) + ' s-v' + sVar;
        t.token.appendChild(slash);
        setTimeout(function() { slash.remove(); }, 240);
    }

    var dmg = Math.max(1, Math.round(u.atk - t.def * 0.32 + Math.random() * 7));
    t.hp -= dmg;
    playSfx('hit');
    updateHp(t);
    t.token.classList.add('hit');
    u.token.classList.add('attacking');
    var pop = t.token.querySelector('.damage-pop');
    if (pop) { pop.innerHTML = '-' + dmg; pop.classList.add('show'); }

    // Impact supplémentaire (différent selon mêlée/distance)
    var impact = document.createElement('div');
    impact.className = 'impact-ring ' + (ranged ? 'impact-ranged' : 'impact-melee');
    impact.style.left = (c2.x - 8) + 'px';
    impact.style.top = (c2.y - 8) + 'px';
    board.appendChild(impact);
    setTimeout(function() { impact.remove(); }, 220);

    var spark = document.createElement('div');
    spark.className = 'spark';
    spark.style.left = (c2.x - 10) + 'px';
    spark.style.top = (c2.y - 10) + 'px';
    board.appendChild(spark);
    setTimeout(function() { spark.remove(); }, 220);

    setTimeout(function() {
        t.token.classList.remove('hit');
        u.token.classList.remove('attacking');
        u.token.classList.remove('cast-shot');
        u.token.classList.remove('melee-swing');
        if (pop) pop.classList.remove('show');
    }, 260);
    if (t.hp <= 0) {
        t.token.classList.add('dead-token');
        var burst = document.createElement('div');
        burst.className = 'death-burst';
        t.token.appendChild(burst);
        playSfx('kill');
        setTimeout(function() { burst.remove(); }, 360);
    }
}

function startFight() {
    if (phase !== 'prep' || fighting) return false;
    if (boardUnits.length === 0) { log('Place au moins une unité sur le plateau.'); return false; }
    var bot = getCurrentBot();
    if (!bot) { log('Victoire ! Tous les bots sont éliminés.'); return false; }
    ensureAudioReady();
    prepareRoundMatchups(bot);
    stopPrepTimer();
    phase = 'fight';
    fighting = true;
    renderHud();
    renderPlayers();
    clearCombatTokens();
    renderBoardUnits();
    combatUnits = [];
    var enemyArmy = botArmy(bot);
    enemyCtx.bonus = getSynergyBonusFromList(enemyArmy);
    enemyCtx.power = 1 + Math.min(1.2, (round * 0.06) + (player.level * 0.03));
    gridMetrics = computeGridMetrics();
    boardUnits.forEach(function(u) { combatUnits.push(makeCombatToken(u, 'player', u.x, u.y)); });
    enemyArmy.forEach(function(u) { combatUnits.push(makeCombatToken(u, 'enemy', u.x, u.y)); });
    log('Combat contre ' + bot.name + ' !');
    fightTimer = setTimeout(fightTick, fightTickMs);
    return true;
}

function stopPrepTimer() {
    if (prepInterval) { clearInterval(prepInterval); prepInterval = null; }
}

function startPrepTimer() {
    stopPrepTimer();
    prepDuration = round >= 8 ? 24 : round >= 5 ? 27 : 30;
    prepRemaining = prepDuration;
    if (prepTimerEl) prepTimerEl.innerHTML = prepRemaining + 's';
    prepInterval = setInterval(function() {
        if (phase !== 'prep' || fighting) { stopPrepTimer(); return; }
        prepRemaining--;
        if (prepTimerEl) prepTimerEl.innerHTML = Math.max(0, prepRemaining) + 's';
        if (timerWrap) timerWrap.className = 'hud-stat timer-stat' + (prepRemaining <= 5 ? ' urgent' : '');
        if (prepRemaining <= 0) {
            stopPrepTimer();
            if (!startFight() && phase === 'prep') startPrepTimer();
        }
    }, 1000);
}

function fightTick() {
    var dt = fightTickMs / 1000;
    var pAlive = alive(combatUnits.filter(function(u) { return u.team === 'player'; }));
    var eAlive = alive(combatUnits.filter(function(u) { return u.team === 'enemy'; }));
    if (pAlive.length === 0 || eAlive.length === 0) { endFight(pAlive, eAlive); return; }
    var actors = alive(combatUnits).sort(function(a, b) { return b.speed - a.speed; });
    actors.forEach(function(u) {
        var targets = u.team === 'player'
            ? alive(combatUnits.filter(function(x) { return x.team === 'enemy'; }))
            : alive(combatUnits.filter(function(x) { return x.team === 'player'; }));
        if (!targets.length || u.hp <= 0) return;
        var t = pickTarget(u, targets) || nearest(u, targets);
        u.attackCd = Math.max(0, (u.attackCd || 0) - dt);
        var dist = distance(u, t);
        var rEff = effectiveRange(u);
        var ranged = isRanged(u);

        // Distance : "kite" basique pour les unités à distance (évite d'être collé)
        if (ranged && dist < 1.05) {
            stepAway(u, t);
            return;
        }

        var inRange = dist <= rEff;
        if (inRange && u.attackCd <= 0) {
            attack(u, t);
            // cooldown un peu plus lent pour les distances, plus nerveux pour la mêlée
            var baseCd = ranged ? 0.62 : 0.52;
            u.attackCd = clamp(baseCd - (u.speed * 0.05), 0.22, 0.70);
        } else if (!inRange) {
            // mêlée = plus agressif, distance = approche plus "douce"
            stepToward(u, t, ranged ? 0.85 : 1.25);
        }
    });
    fightTimer = setTimeout(fightTick, fightTickMs);
}

function incomeForRound() {
    var base;
    if (round <= 2) base = 2;
    else if (round === 3) base = 3;
    else if (round === 4) base = 4;
    else base = 5;
    var interest = Math.min(5, Math.floor(player.gold / 10));
    var streak = 0;
    var streakAbs = Math.abs(player.streak);
    if (streakAbs >= 2) streak = 1;
    if (streakAbs >= 3) streak = 2;
    if (streakAbs >= 5) streak = 3;
    return { total: base + interest + streak, base: base, interest: interest, streak: streak };
}

function endFight(pAlive, eAlive) {
    clearTimeout(fightTimer);
    var bot = getCurrentBot();
    fighting = false;
    phase = 'prep';
    var roundsPlayed = round;
    var dmg = 0;
    var incomeParts = incomeForRound();
    var income = incomeParts.total;
    var msg = '';
    if (pAlive.length > 0) {
        dmg = 4 + Math.floor(round / 2) + pAlive.length;
        bot.hp = Math.max(0, bot.hp - dmg);
        if (bot.hp <= 0) bot.dead = true;
        player.streak = player.streak >= 0 ? player.streak + 1 : 1;
        player.gold += income;
        player.xp += 2;
        bot.gold = (bot.gold || 0) + 3;
        bot.xp = (bot.xp || 0) + 1;
        botLevelUpCheck(bot);
        playSfx('win');
        msg = 'Victoire ! ' + bot.name + ' perd ' + dmg + ' PV. +' + income + ' PO (' + incomeParts.base + '+' + incomeParts.interest + '+' + incomeParts.streak + '), +2 XP.';
    } else {
        dmg = 4 + Math.floor(round / 2) + eAlive.length;
        player.hp = Math.max(0, player.hp - dmg);
        player.streak = player.streak <= 0 ? player.streak - 1 : -1;
        player.gold += income;
        player.xp += 1;
        bot.gold = (bot.gold || 0) + 4;
        bot.xp = (bot.xp || 0) + 2;
        botLevelUpCheck(bot);
        playSfx('lose');
        msg = 'Défaite — tu perds ' + dmg + ' PV. +' + income + ' PO (' + incomeParts.base + '+' + incomeParts.interest + '+' + incomeParts.streak + '), +1 XP.';
    }
    levelUpCheck();

    // Les bots se battent aussi entre eux à chaque round (simulé)
    var botMsg = resolveBotBattles(bot);
    if (botMsg) msg += '<br><small style="color:var(--muted)">' + botMsg + '</small>';
    log(msg);

    currentBotIndex = (currentBotIndex + 1) % bots.length;
    round++;
    // Nettoie l'affichage des matchups en phase de préparation
    prepareRoundMatchups(null);
    if (player.hp <= 0) {
        log('Partie terminée — tes PV sont à 0.');
    }
    else if (!getCurrentBot(false)) {
        log('Victoire finale ! Tous les bots éliminés.');
    }
    else fillShop();
    clearCombatTokens();
    combatUnits = [];
    renderAll();
    if (phase === 'prep' && player.hp > 0 && getCurrentBot(false)) startPrepTimer();
}

document.getElementById('buyXp').addEventListener('click', buyXp);
document.getElementById('rerollShop').addEventListener('click', rerollShop);
document.getElementById('startFight').addEventListener('click', startFight);
if (sellBtn) {
    sellBtn.addEventListener('click', function() {
        if (phase !== 'prep') return;
        if (!selectedUid) return;
        if (sellUnitByUid(selectedUid)) {
            selectedUid = null;
            renderAll();
        }
    });
}
document.getElementById('settingsBtn').addEventListener('click', function() {
    document.getElementById('settingsModal').classList.remove('hidden');
    ensureAudioReady();
});
document.getElementById('closeSettings').addEventListener('click', function() {
    document.getElementById('settingsModal').classList.add('hidden');
});
var abandonBtn = document.getElementById('abandonGame');
if (abandonBtn) {
    abandonBtn.addEventListener('click', function() {
        window.location.href = 'index.php';
    });
}
window.addEventListener('resize', function() {
    gridMetrics = computeGridMetrics();
    combatUnits.forEach(function(u) { moveCombatToken(u, u.x, u.y); });
});

document.addEventListener('pointerdown', ensureAudioReady, { once: true });
document.addEventListener('keydown', ensureAudioReady, { once: true });
document.addEventListener('pointerdown', startBackgroundMusic, { once: true });
document.addEventListener('keydown', startBackgroundMusic, { once: true });
document.getElementById('toggleSound').addEventListener('click', function() {
    soundEnabled = !soundEnabled;
    this.textContent = soundEnabled ? '🔊' : '🔇';
    this.classList.toggle('muted', !soundEnabled);
    if (soundEnabled) {
        ensureAudioReady();
        startBackgroundMusic();
        playTone(700, 0.07, 0.04, 'triangle');
    } else {
        stopBackgroundMusic();
    }
});

createBoard();
fillShop();
renderAll();
log('Bienvenue dans la faille de l\'invocateur. Départ à 10 PO, intérêt et série actifs.');
startPrepTimer();
