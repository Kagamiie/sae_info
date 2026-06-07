<?php
session_start();
include("includes/connexion.php");
$message = "";

if (isset($_POST['pseudo']) && isset($_POST['mot_de_passe'])) {
    $pseudo = mysqli_real_escape_string($connexion, trim($_POST['pseudo']));
    $motDePasse = mysqli_real_escape_string($connexion, $_POST['mot_de_passe']);

    // Requête avec données utilisateur (WHERE)
    $sql = "SELECT id, pseudo, role, statut
            FROM utilisateur
            WHERE pseudo='$pseudo' AND mot_de_passe='$motDePasse'
            LIMIT 1";
    $resultat = mysqli_query($connexion, $sql);

    if ($resultat && mysqli_num_rows($resultat) > 0) {
        $ligne = mysqli_fetch_assoc($resultat);
        $statut = isset($ligne['statut']) ? $ligne['statut'] : 'actif';
        if ($statut == 'suspendu') {
            $message = "Compte suspendu. Contacte un administrateur.";
        } else {
            $_SESSION['id'] = $ligne['id'];
            $_SESSION['pseudo'] = $ligne['pseudo'];
            $_SESSION['role'] = $ligne['role'];
            header("Location: index.php");
            exit();
        }
    } else {
        $message = "Identifiants incorrects";
    }
}
include("includes/header.php");
?>
<section class="auth">
    <form method="post" class="form" autocomplete="off">
        <h1>Connexion</h1>
        <p class="info">Comptes de test : root/root ou joueur/joueur</p>
        <input type="text" name="pseudo" placeholder="Pseudo" required autocomplete="off">
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required autocomplete="new-password">
        <button class="btn" type="submit">Se connecter</button>
        <p class="error"><?php echo $message; ?></p>
        <p class="info" style="margin-top:6px;">Pas de compte ? <a href="inscription.php" style="color:var(--gold-light);">Inscription</a></p>
    </form>
</section>
<?php include("includes/footer.php"); ?>
