<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header("Location: login.php");
    exit();
}
include("includes/connexion.php");
$resultat = mysqli_query($connexion, "SELECT * FROM personnage ORDER BY cout ASC, nom ASC");
$personnages = array();
while ($ligne = mysqli_fetch_assoc($resultat)) {
    $personnages[] = $ligne;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partie — Real X TFT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
</head>
<body class="tft-game">

<button id="settingsBtn" class="tft-settings" title="Paramètres">⚙</button>

<div class="tft-app">

    <aside class="tft-leaderboard">
        <div class="tft-round-box">
            <span class="round-num">Round <span id="round">1</span></span>
            <span class="round-phase" id="phaseLabel">Préparation</span>
        </div>
        <div id="playersList" class="tft-players"></div>
    </aside>

    <div class="tft-center">
        <header class="tft-hud">
            <div class="hud-stat"><span class="hud-icon">👤</span> <b><?php echo htmlspecialchars($_SESSION['pseudo']); ?></b></div>
            <div class="hud-stat hp-stat"><span class="hud-icon">❤</span> <b id="playerHp">100</b></div>
            <div class="hud-stat"><span class="hud-icon">⬡</span> Niv. <b id="playerLevel">1</b></div>
            <div class="hud-stat"><span class="hud-icon">✦</span> <b id="playerXp">0/2</b></div>
            <div class="hud-stat gold-stat"><span class="hud-icon">🪙</span> <b id="playerGold">10</b></div>
            <div class="hud-stat timer-stat" id="timerWrap"><span class="hud-icon">⏱</span> <b id="prepTimer">30s</b></div>
            <div class="hud-stat"><span class="hud-icon">⚔</span> <b id="enemyName">Aatrox Noirfer</b></div>
            <button id="toggleSound" class="hud-sound-btn" type="button" title="Activer / couper le son">🔊</button>
        </header>

        <section class="tft-arena">
            <div class="tft-arena-inner">
                <div id="board" class="tft-board"></div>
                <div id="combatLog" class="tft-log">Achète des champions, place-les sur le plateau, puis lance le combat.</div>
            </div>
        </section>

        <div class="tft-bench-wrap">
            <div class="tft-bench-label">Banc</div>
            <div id="bench" class="tft-bench"></div>
        </div>
    </div>

    <aside class="tft-traits">
        <div class="tft-panel">
            <h3>Traits actifs</h3>
            <div id="synergyPanel" class="synergy-list"></div>
        </div>
        <div class="tft-panel">
            <h3>Aide</h3>
            <p>Départ à 10 PO, intérêt +1 PO par tranche de 10 (max 5), bonus de série à partir de 2 victoires/défaites. Place tes unités sur les 2 lignes du bas. 2 copies identiques fusionnent. Double-clic pour vendre.</p>
        </div>
    </aside>

    <footer class="tft-shop">
        <div class="tft-shop-side">
            <button id="buyXp" class="btn">Monter<br><small>4 PO → +4 XP</small></button>
            <button id="rerollShop" class="btn secondary">Reroll<br><small>2 PO</small></button>
            <button id="sellSelected" class="btn secondary" type="button" disabled>Vendre<br><small id="sellHint">Sélectionne une unité</small></button>
        </div>
        <div id="shopCards" class="tft-shop-cards"></div>
        <button id="startFight" class="btn btn-fight">COMBAT</button>
    </footer>

</div>

<div id="settingsModal" class="settings-modal hidden">
    <div class="settings-panel panel">
        <h2>Paramètres</h2>
        <p>Quitter la partie revient au menu. La progression ne sera pas sauvegardée.</p>
        <div class="settings-actions">
            <button id="closeSettings" class="btn secondary">Retour</button>
            <button id="abandonGame" type="button" class="btn danger">Abandonner</button>
        </div>
    </div>
</div>

<div id="spectateModal" class="settings-modal hidden">
    <div class="settings-panel panel">
        <h2>Spectateur</h2>
        <p id="spectateTitle" style="margin-top:-6px;color:var(--muted);">Choisis un combat de bots à regarder.</p>
        <div class="settings-actions">
            <button id="closeSpectate" class="btn secondary" type="button">Fermer</button>
        </div>
    </div>
</div>

<script>
window.INITIAL_UNITS = <?php echo json_encode($personnages); ?>;
window.PLAYER_NAME = <?php echo json_encode($_SESSION['pseudo']); ?>;
</script>
<script src="js/game.js"></script>
</body>
</html>
