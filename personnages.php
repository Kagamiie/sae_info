<?php
session_start();
include("includes/connexion.php");
include("includes/header.php");

// 1) Requête simple (affichage catalogue)
$resultat = mysqli_query($connexion, "SELECT * FROM personnage ORDER BY cout ASC, nom ASC");

// 2) Requête avec GROUP BY + HAVING (démonstration cours)
$resGroupHaving = mysqli_query(
    $connexion,
    "SELECT cout, COUNT(*) AS nb_personnages
     FROM personnage
     GROUP BY cout
     HAVING COUNT(*) >= 2
     ORDER BY cout"
);

// 3) Requête avec agrégations (MIN / MAX / AVG)
$resAgg = mysqli_query(
    $connexion,
    "SELECT MIN(pv) AS pv_min, MAX(pv) AS pv_max, AVG(pv) AS pv_moyen, AVG(attaque) AS attaque_moyenne
     FROM personnage"
);
$agg = $resAgg ? mysqli_fetch_assoc($resAgg) : null;

// 4) Requête imbriquée (sous-requête) : persos plus chers que la moyenne
$resSub = mysqli_query(
    $connexion,
    "SELECT id, nom, cout
     FROM personnage
     WHERE cout > (SELECT AVG(cout) FROM personnage)
     ORDER BY cout DESC, nom"
);
?>
<h1 class="page-title">Champions</h1>
<p style="text-align:center;color:var(--muted);margin:-12px 0 24px;">Tous les personnages disponibles en partie. Les traits activent des bonus de synergie.</p>

<section class="panel" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Stats (démonstration SQL)</h2>
    <p style="color:var(--muted);margin-top:-6px;">Exemples : agrégations, GROUP BY/HAVING, sous-requête.</p>

    <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
        <div class="panel" style="background:var(--bg-panel-2);">
            <h3 style="margin-top:0;">Agrégations</h3>
            <?php if ($agg) { ?>
                <p>PV min : <b><?php echo (int)$agg['pv_min']; ?></b></p>
                <p>PV max : <b><?php echo (int)$agg['pv_max']; ?></b></p>
                <p>PV moyen : <b><?php echo (int)round((float)$agg['pv_moyen']); ?></b></p>
                <p>ATQ moyenne : <b><?php echo (int)round((float)$agg['attaque_moyenne']); ?></b></p>
            <?php } else { ?>
                <p class="error">Erreur requête agrégations.</p>
            <?php } ?>
        </div>

        <div class="panel" style="background:var(--bg-panel-2);">
            <h3 style="margin-top:0;">GROUP BY + HAVING</h3>
            <?php if ($resGroupHaving) { ?>
                <ul style="margin:0;padding-left:18px;">
                    <?php while ($r = mysqli_fetch_assoc($resGroupHaving)) { ?>
                        <li>Coût <b><?php echo (int)$r['cout']; ?></b> : <?php echo (int)$r['nb_personnages']; ?> personnages</li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p class="error">Erreur requête GROUP BY/HAVING.</p>
            <?php } ?>
        </div>

        <div class="panel" style="background:var(--bg-panel-2);">
            <h3 style="margin-top:0;">Sous-requête</h3>
            <p style="color:var(--muted);margin-top:-6px;">Personnages plus chers que le coût moyen :</p>
            <?php if ($resSub) { ?>
                <ul style="margin:0;padding-left:18px;max-height:140px;overflow:auto;">
                    <?php while ($r = mysqli_fetch_assoc($resSub)) { ?>
                        <li><?php echo htmlspecialchars($r['nom']); ?> — <b><?php echo (int)$r['cout']; ?> PO</b></li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p class="error">Erreur requête imbriquée.</p>
            <?php } ?>
        </div>
    </div>
</section>

<div class="cards">
<?php while ($p = mysqli_fetch_assoc($resultat)) { ?>
    <div class="champ-card cost-<?php echo (int)$p['cout']; ?>">
        <img src="uploads/<?php echo htmlspecialchars($p['icone']); ?>" alt="<?php echo htmlspecialchars($p['nom']); ?>">
        <div class="champ-body">
            <span class="champ-cost"><?php echo (int)$p['cout']; ?> PO</span>
            <h2><?php echo htmlspecialchars($p['nom']); ?></h2>
            <p>ATQ <?php echo (int)$p['attaque']; ?> · DEF <?php echo (int)$p['defense']; ?> · PV <?php echo (int)$p['pv']; ?></p>
            <p>VIT <?php echo (int)$p['vitesse']; ?> · Portée <?php echo (int)$p['portee']; ?></p>
            <div class="champ-traits">
                <span class="trait-tag"><?php echo htmlspecialchars($p['origine']); ?></span>
                <span class="trait-tag"><?php echo htmlspecialchars($p['classe']); ?></span>
            </div>
        </div>
    </div>
<?php } ?>
</div>
<?php include("includes/footer.php"); ?>
