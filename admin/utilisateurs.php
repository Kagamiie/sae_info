<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'root') {
    header("Location: ../login.php");
    exit();
}
include("../includes/connexion.php");

$message = "";
$currentId = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;

// Actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($userId <= 0) {
        $message = "Utilisateur invalide.";
    } elseif ($userId == $currentId) {
        $message = "Action refusée : tu ne peux pas modifier ton propre compte.";
    } else {
        if ($action == 'toggle_suspend') {
            $newStatus = isset($_POST['new_status']) ? $_POST['new_status'] : 'actif';
            if ($newStatus != 'actif' && $newStatus != 'suspendu') {
                $newStatus = 'actif';
            }
            $newStatusEsc = mysqli_real_escape_string($connexion, $newStatus);
            $ok = mysqli_query($connexion, "UPDATE utilisateur SET statut='$newStatusEsc' WHERE id=$userId LIMIT 1");
            if ($ok) $message = ($newStatus == 'suspendu') ? "Compte suspendu." : "Compte réactivé.";
            else $message = "Erreur serveur (requête).";
        }
        elseif ($action == 'set_role') {
            $newRole = isset($_POST['new_role']) ? $_POST['new_role'] : 'joueur';
            if ($newRole != 'joueur' && $newRole != 'root') {
                $newRole = 'joueur';
            }
            $newRoleEsc = mysqli_real_escape_string($connexion, $newRole);
            $ok = mysqli_query($connexion, "UPDATE utilisateur SET role='$newRoleEsc' WHERE id=$userId LIMIT 1");
            if ($ok) $message = "Rôle mis à jour.";
            else $message = "Erreur serveur (requête).";
        }
        elseif ($action == 'delete_user') {
            // Pour éviter l'échec de suppression à cause des FK, on supprime d'abord les dépendances.
            // (Le projet ne sauvegarde pas réellement les parties, mais la DB peut en contenir.)
            mysqli_query($connexion, "DELETE FROM partie WHERE id_utilisateur = $userId");
            $ok = mysqli_query($connexion, "DELETE FROM utilisateur WHERE id = $userId LIMIT 1");
            if ($ok) $message = "Utilisateur supprimé.";
            else $message = "Erreur lors de la suppression.";
        } else {
            $message = "Action inconnue.";
        }
    }
}

// Liste utilisateurs + nb parties (jointure)
$res = mysqli_query(
    $connexion,
    "SELECT u.id, u.pseudo, u.role, u.statut,
            p.nb_parties AS nb_parties
     FROM utilisateur u
     LEFT JOIN (SELECT id_utilisateur, COUNT(*) AS nb_parties FROM partie GROUP BY id_utilisateur) p
       ON p.id_utilisateur = u.id
     ORDER BY u.id DESC"
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin utilisateurs — Real X TFT</title>
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

<h1 class="page-title">Administration des utilisateurs</h1>
<?php if ($message) { ?><p class="success"><?php echo htmlspecialchars($message); ?></p><?php } ?>

<section class="panel" style="overflow:auto;">
    <table style="width:100%;border-collapse:collapse;min-width:760px;">
        <thead>
        <tr style="text-align:left;color:var(--muted);font-size:13px;">
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">ID</th>
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">Pseudo</th>
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">Rôle</th>
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">Statut</th>
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">Parties</th>
            <th style="padding:10px 8px;border-bottom:1px solid var(--border);">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($res && mysqli_num_rows($res) > 0) { ?>
            <?php while ($u = mysqli_fetch_assoc($res)) {
                $uid = (int)$u['id'];
                $isSelf = ($uid == $currentId);
                $statut = isset($u['statut']) ? $u['statut'] : 'actif';
                $role = isset($u['role']) ? $u['role'] : 'joueur';
                $nbParties = isset($u['nb_parties']) ? intval($u['nb_parties']) : 0;
                ?>
                <tr>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);"><?php echo $uid; ?></td>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($u['pseudo']); ?><?php if ($isSelf) echo " (toi)"; ?></td>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($role); ?></td>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($statut); ?></td>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);"><?php echo $nbParties; ?></td>
                    <td style="padding:10px 8px;border-bottom:1px solid var(--border);">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                            <form method="post" style="display:flex;gap:6px;align-items:center;">
                                <input type="hidden" name="action" value="set_role">
                                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                                <select name="new_role" <?php echo $isSelf ? 'disabled' : ''; ?>>
                                    <option value="joueur" <?php echo $role == 'joueur' ? 'selected' : ''; ?>>joueur</option>
                                    <option value="root" <?php echo $role == 'root' ? 'selected' : ''; ?>>root</option>
                                </select>
                                <button class="btn secondary" type="submit" <?php echo $isSelf ? 'disabled' : ''; ?>>Rôle</button>
                            </form>

                            <form method="post">
                                <input type="hidden" name="action" value="toggle_suspend">
                                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                                <input type="hidden" name="new_status" value="<?php echo $statut == 'suspendu' ? 'actif' : 'suspendu'; ?>">
                                <button class="btn secondary" type="submit" <?php echo $isSelf ? 'disabled' : ''; ?>>
                                    <?php echo $statut == 'suspendu' ? 'Réactiver' : 'Suspendre'; ?>
                                </button>
                            </form>

                            <form method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                                <button class="btn danger" type="submit" <?php echo $isSelf ? 'disabled' : ''; ?>>Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="6" style="padding:14px 8px;color:var(--muted);">Aucun utilisateur.</td></tr>
        <?php } ?>
        </tbody>
    </table>
</section>

</main>
</body>
</html>
