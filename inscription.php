<?php
session_start();
include("includes/connexion.php");

if (isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$success = false;

if (isset($_POST['pseudo'], $_POST['mot_de_passe'], $_POST['mot_de_passe2'])) {
    $pseudoRaw = trim($_POST['pseudo']);
    $mdpRaw = $_POST['mot_de_passe'];
    $mdp2Raw = $_POST['mot_de_passe2'];

    if ($pseudoRaw == "" || $mdpRaw == "" || $mdp2Raw == "") {
        $message = "Tous les champs sont obligatoires.";
    } elseif (strlen($pseudoRaw) < 3 || strlen($pseudoRaw) > 20) {
        $message = "Le pseudo doit faire entre 3 et 20 caractères.";
    } elseif ($mdpRaw != $mdp2Raw) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $pseudo = mysqli_real_escape_string($connexion, $pseudoRaw);
        $mdp = mysqli_real_escape_string($connexion, $mdpRaw);

        // Vérifie l'unicité du pseudo (requête avec WHERE)
        $res = mysqli_query($connexion, "SELECT id FROM utilisateur WHERE pseudo='$pseudo' LIMIT 1");
        $exists = $res && mysqli_fetch_assoc($res);

        if ($exists) {
            $message = "Ce pseudo est déjà utilisé.";
        } else {
            // Création du compte (INSERT)
            $role = "joueur";
            $sqlIns = "INSERT INTO utilisateur (pseudo, mot_de_passe, role, statut)
                       VALUES ('$pseudo', '$mdp', '$role', 'actif')";
            $ok = mysqli_query($connexion, $sqlIns);
            if ($ok) {
                $newId = mysqli_insert_id($connexion);
                $_SESSION['id'] = $newId;
                $_SESSION['pseudo'] = $pseudoRaw;
                $_SESSION['role'] = $role;
                header("Location: index.php");
                exit();
            } else {
                $message = "Erreur lors de la création du compte.";
            }
        }
    }
}

include("includes/header.php");
?>

<section class="auth">
    <form method="post" class="form" autocomplete="off">
        <h1>Inscription</h1>
        <p class="info">Crée ton compte pour jouer à Real X TFT.</p>
        <input type="text" name="pseudo" placeholder="Pseudo" required autocomplete="off" value="<?php echo isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : ''; ?>">
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required autocomplete="new-password">
        <input type="password" name="mot_de_passe2" placeholder="Confirmer le mot de passe" required autocomplete="new-password">
        <button class="btn" type="submit">Créer mon compte</button>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <p class="info" style="margin-top:6px;">Déjà un compte ? <a href="login.php" style="color:var(--gold-light);">Connexion</a></p>
    </form>
</section>

<?php include("includes/footer.php"); ?>
