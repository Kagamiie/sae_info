<?php
// Session : démarrée dans chaque page (style cours)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real X TFT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
</head>
<body>
<header class="topbar">
    <a class="logo" href="index.php">Real X TFT</a>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="jeu.php">Jouer</a>
        <a href="personnages.php">Personnages</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'root') { ?>
            <a href="admin/personnages.php">Admin</a>
            <a href="admin/utilisateurs.php">Utilisateurs</a>
        <?php } ?>
        <?php if (isset($_SESSION['pseudo'])) { ?>
            <a href="logout.php">Déconnexion</a>
        <?php } else { ?>
            <a href="login.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
        <?php } ?>
    </nav>
</header>
<main>
