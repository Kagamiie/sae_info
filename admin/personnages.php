<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'root') {
    header("Location: ../login.php");
    exit();
}
include("../includes/connexion.php");
$message = "";

if (isset($_POST['ajouter'])) {
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $cout = intval($_POST['cout']);
    $attaque = intval($_POST['attaque']);
    $defense = intval($_POST['defense']);
    $pv = intval($_POST['pv']);
    $vitesse = intval($_POST['vitesse']);
    $portee = intval($_POST['portee']);
    $origine = mysqli_real_escape_string($connexion, $_POST['origine']);
    $classe = mysqli_real_escape_string($connexion, $_POST['classe']);
    $icone = "default.png";

    if (isset($_FILES['icone']) && $_FILES['icone']['error'] == 0) {
        $extension = strtolower(pathinfo($_FILES['icone']['name'], PATHINFO_EXTENSION));
        $extensionsAutorisees = array("jpg", "jpeg", "png", "gif", "webp");
        if (in_array($extension, $extensionsAutorisees)) {
            $icone = uniqid("unit_") . "." . $extension;
            move_uploaded_file($_FILES['icone']['tmp_name'], "../uploads/" . $icone);
        }
    }

    $sql = "INSERT INTO personnage (nom, cout, attaque, defense, pv, vitesse, portee, origine, classe, icone)
            VALUES ('$nom', $cout, $attaque, $defense, $pv, $vitesse, $portee, '$origine', '$classe', '$icone')";
    mysqli_query($connexion, $sql);
    $message = "Personnage ajouté.";
}

if (isset($_POST['modifier'])) {
    $id = intval($_POST['id']);
    $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
    $cout = intval($_POST['cout']);
    $attaque = intval($_POST['attaque']);
    $defense = intval($_POST['defense']);
    $pv = intval($_POST['pv']);
    $vitesse = intval($_POST['vitesse']);
    $portee = intval($_POST['portee']);
    $origine = mysqli_real_escape_string($connexion, $_POST['origine']);
    $classe = mysqli_real_escape_string($connexion, $_POST['classe']);

    $sqlIcone = "";
    if (isset($_FILES['icone']) && $_FILES['icone']['error'] == 0) {
        $extension = strtolower(pathinfo($_FILES['icone']['name'], PATHINFO_EXTENSION));
        $extensionsAutorisees = array("jpg", "jpeg", "png", "gif", "webp");
        if (in_array($extension, $extensionsAutorisees)) {
            $icone = uniqid("unit_") . "." . $extension;
            move_uploaded_file($_FILES['icone']['tmp_name'], "../uploads/" . $icone);
            $sqlIcone = ", icone='$icone'";
        }
    }

    $sql = "UPDATE personnage SET nom='$nom', cout=$cout, attaque=$attaque, defense=$defense, pv=$pv, vitesse=$vitesse, portee=$portee, origine='$origine', classe='$classe' $sqlIcone WHERE id=$id";
    mysqli_query($connexion, $sql);
    $message = "Personnage modifié.";
}

if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    mysqli_query($connexion, "DELETE FROM personnage WHERE id=$id");
    $message = "Personnage supprimé.";
}

$personnageAModifier = null;
if (isset($_GET['modifier'])) {
    $id = intval($_GET['modifier']);
    $resModif = mysqli_query($connexion, "SELECT * FROM personnage WHERE id=$id");
    $personnageAModifier = mysqli_fetch_assoc($resModif);
}

$resultat = mysqli_query($connexion, "SELECT * FROM personnage ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Real X TFT</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../favicon.svg" type="image/svg+xml">
</head>
<body>
<header class="topbar">
    <a class="logo" href="../index.php">Real X TFT</a>
    <nav>
        <a href="../index.php">Accueil</a>
        <a href="../jeu.php">Jouer</a>
        <a href="../personnages.php">Personnages</a>
        <a href="personnages.php">Admin persos</a>
        <a href="utilisateurs.php">Utilisateurs</a>
        <a href="../logout.php">Déconnexion</a>
    </nav>
</header>
<main>
<h1 class="page-title">Administration des personnages</h1>
<?php if ($message) { ?><p class="success"><?php echo htmlspecialchars($message); ?></p><?php } ?>
<section class="admin-grid">
    <form class="form panel" method="post" enctype="multipart/form-data">
        <?php if ($personnageAModifier) { ?>
            <h2>Modifier un personnage</h2>
            <input type="hidden" name="id" value="<?php echo $personnageAModifier['id']; ?>">
            <input type="text" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($personnageAModifier['nom']); ?>" required>
            <input type="number" name="cout" placeholder="Coût (1-5)" min="1" max="5" value="<?php echo $personnageAModifier['cout']; ?>" required>
            <input type="number" name="attaque" placeholder="Attaque" value="<?php echo $personnageAModifier['attaque']; ?>" required>
            <input type="number" name="defense" placeholder="Défense" value="<?php echo $personnageAModifier['defense']; ?>" required>
            <input type="number" name="pv" placeholder="PV" value="<?php echo $personnageAModifier['pv']; ?>" required>
            <input type="number" name="vitesse" placeholder="Vitesse" value="<?php echo $personnageAModifier['vitesse']; ?>" required>
            <input type="number" name="portee" placeholder="Portée" value="<?php echo $personnageAModifier['portee']; ?>" required>
            <input type="text" name="origine" placeholder="Origine / Trait 1" value="<?php echo htmlspecialchars($personnageAModifier['origine']); ?>" required>
            <input type="text" name="classe" placeholder="Classe / Trait 2" value="<?php echo htmlspecialchars($personnageAModifier['classe']); ?>" required>
            <label>Nouvelle icône (facultatif)</label>
            <input type="file" name="icone" accept="image/*">
            <button class="btn" name="modifier" type="submit">Enregistrer</button>
            <a class="btn secondary" href="personnages.php">Annuler</a>
        <?php } else { ?>
            <h2>Ajouter un personnage</h2>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="number" name="cout" placeholder="Coût (1-5)" min="1" max="5" required>
            <input type="number" name="attaque" placeholder="Attaque" required>
            <input type="number" name="defense" placeholder="Défense" required>
            <input type="number" name="pv" placeholder="PV" required>
            <input type="number" name="vitesse" placeholder="Vitesse" required>
            <input type="number" name="portee" placeholder="Portée" required>
            <input type="text" name="origine" placeholder="Origine / Trait 1" required>
            <input type="text" name="classe" placeholder="Classe / Trait 2" required>
            <label>Icône carrée</label>
            <input type="file" name="icone" accept="image/*">
            <button class="btn" name="ajouter" type="submit">Ajouter</button>
        <?php } ?>
    </form>
    <div class="panel">
        <h2>Liste des champions (<?php echo mysqli_num_rows($resultat); ?>)</h2>
        <div class="admin-list">
        <?php while ($p = mysqli_fetch_assoc($resultat)) { ?>
            <div class="admin-row">
                <img src="../uploads/<?php echo htmlspecialchars($p['icone']); ?>" alt="">
                <div>
                    <strong><?php echo htmlspecialchars($p['nom']); ?></strong>
                    <span class="trait-tag" style="margin-left:6px;"><?php echo (int)$p['cout']; ?> PO</span><br>
                    ATQ <?php echo (int)$p['attaque']; ?> · DEF <?php echo (int)$p['defense']; ?> · PV <?php echo (int)$p['pv']; ?><br>
                    VIT <?php echo (int)$p['vitesse']; ?> · Portée <?php echo (int)$p['portee']; ?><br>
                    <span class="trait-tag"><?php echo htmlspecialchars($p['origine']); ?></span>
                    <span class="trait-tag"><?php echo htmlspecialchars($p['classe']); ?></span>
                </div>
                <div class="admin-actions">
                    <a href="personnages.php?modifier=<?php echo $p['id']; ?>">Modifier</a><br>
                    <a class="delete" href="personnages.php?supprimer=<?php echo $p['id']; ?>" onclick="return confirm('Supprimer ce personnage ?');">Supprimer</a>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
</section>
</main>
<footer class="footer"><p>Administration root — Real X TFT</p></footer>
</body>
</html>
