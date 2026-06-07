<?php
session_start();
include "includes/header.php";
?>

<section class="page-acceuil">
    <div class="page-acceuil-contenu">
        <h1>Real X TFT</h1>
        <p class="sous-titre">Le jeu d'auto-battler ultime. Construis ton équipe, active tes synergies, fusionne tes champions et élimine 8 adversaires pour devenir le champion.</p>

        <div class="actions">
            <a class="btn btn-gold" href="jeu.php">⚔️ Lancer une partie</a>
            <a class="btn btn-border" href="personnages.php">📖 Voir les champions</a>
        </div>

        <div class="stats">
            <div class="stat">
                <span class="stat-nombre">180+</span>
                <span class="stat-label">Combinaisons possibles</span>
            </div>
            <div class="stat">
                <span class="stat-nombre">9 Traits</span>
                <span class="stat-label">Synergies à découvrir</span>
            </div>
            <div class="stat">
                <span class="stat-nombre">100% PHP</span>
                <span class="stat-label">Entièrement fait maison</span>
            </div>
        </div>
    </div>
</section>

<!-- GAMEPLAY LOOP -->
<section class="page-gameplay">
    <div class="section-titre">
        <h2>Comment ça marche ?</h2>
        <p>Une boucle de jeu simple mais stratégique</p>
    </div>

    <div class="cartes-grille">
        <div class="carte">
            <div class="numero">1</div>
            <h3>🛒 Boutique</h3>
            <p>À chaque round, achète jusqu'à 5 champions dans la boutique. Chaque champion a un coût différent et des stats uniques.</p>
        </div>
        <div class="carte">
            <div class="numero">2</div>
            <h3>📍 Placement</h3>
            <p>Place tes unités sur le plateau 5×4. Le positionnement est crucial pour maximiser tes synergies et ta défense.</p>
        </div>
        <div class="carte">
            <div class="numero">3</div>
            <h3>⚡ Fusion</h3>
            <p>Fusionne 3 champions identiques pour obtenir un 2★, puis 3 copies du 2★ pour un 3★ encore plus puissant.</p>
        </div>
        <div class="carte">
            <div class="numero">4</div>
            <h3>⚙️ Synergies</h3>
            <p>Combine tes champions pour activer des traits. Plus tu as de champions du même trait, plus l'effet est puissant.</p>
        </div>
        <div class="carte">
            <div class="numero">5</div>
            <h3>⚔️ Combat</h3>
            <p>Tes unités se battent automatiquement. Utilise l'ordre de combat et le positionnement pour remporter la victoire.</p>
        </div>
        <div class="carte">
            <div class="numero">6</div>
            <h3>🏆 Victoire</h3>
            <p>Élimine les 8 adversaires et deviens le champion ! Attention : tu commences avec 100 PV, chaque défaite te coûte de la vie.</p>
        </div>
    </div>
</section>

<!-- KEY FEATURES -->
<section class="page-features">
    <div class="section-titre">
        <h2>Fonctionnalités principales</h2>
        <p>Tout ce qu'il faut pour jouer et bien jouer</p>
    </div>

    <div class="features-grille">
        <article class="feature">
            <div class="icone">🛒</div>
            <h3>Boutique & Board</h3>
            <p>Achète 5 champions par round et place-les stratégiquement sur ton plateau 5×4. Gère ton banc et tes points de placement avec précision.</p>
            <ul>
                <li>Plateau 5×4 interactif</li>
                <li>Banc de 9 champions</li>
                <li>Achat/vente en direct</li>
            </ul>
        </article>

        <article class="feature">
            <div class="icone">⚔️</div>
            <h3>Combat Automatique</h3>
            <p>Tes unités se battent seules selon tes ordres de bataille. Synergies, fusions 3★ et progression naturelle comme sur TFT.</p>
            <ul>
                <li>Animations de combat fluides</li>
                <li>Système de synergies avancé</li>
                <li>Fusions intelligentes</li>
            </ul>
        </article>

        <article class="feature">
            <div class="icone">📊</div>
            <h3>Statistiques & Progression</h3>
            <p>Gère tes économies, ton XP et ta santé. Montage de niveau pour accéder à plus d'emplacements sur le board.</p>
            <ul>
                <li>Système d'économie dynamique</li>
                <li>Montée de niveau progressive</li>
                <li>Barre de santé en temps réel</li>
            </ul>
        </article>

        <article class="feature">
            <div class="icone">🌟</div>
            <h3>9 Traits Uniques</h3>
            <p>Des synergies variées pour créer des compositions uniques. Combine les traits pour des effets puissants et stratégiques.</p>
            <ul>
                <li>Traits de coût 1-5</li>
                <li>Effets cumulatifs</li>
                <li>Bonifications progressives</li>
            </ul>
        </article>

        <article class="feature">
            <div class="icone">🤖</div>
            <h3>8 Adversaires IA</h3>
            <p>Affronte 8 bots avec des stratégies différentes. Chaque adversaire a ses propres tendances de composition.</p>
            <ul>
                <li>IA adaptative</li>
                <li>Affichage des compositions</li>
                <li>Spectateur de combats</li>
            </ul>
        </article>

        <article class="feature">
            <div class="icone">⚙️</div>
            <h3>Admin Complet</h3>
            <p>Accès root pour ajouter, modifier et supprimer les champions avec leurs stats, traits et icônes personnalisés.</p>
            <ul>
                <li>Gestion des champions</li>
                <li>Gestion des traits</li>
                <li>Gestion des utilisateurs</li>
            </ul>
        </article>
    </div>
</section>

<!-- QUICK START -->
<section class="page-demarrage">
    <div class="section-titre">
        <h2>Commencer en 3 étapes</h2>
        <p>Prêt à jouer ? C'est simple</p>
    </div>

    <div class="demarrage-grille">
        <div class="demarrage-carte">
            <div class="etape">1</div>
            <h3><?php if (isset($_SESSION["pseudo"])) {
                echo "Tu es connecté ✓";
            } else {
                echo "Crée un compte";
            } ?></h3>
            <p>
                <?php if (isset($_SESSION["pseudo"])) {
                    echo "Bienvenue " .
                        htmlspecialchars($_SESSION["pseudo"]) .
                        " !";
                } else {
                    echo "Inscris-toi ou connecte-toi pour accéder au jeu complet.";
                } ?>
            </p>
            <?php if (!isset($_SESSION["pseudo"])) { ?>
                <a class="btn btn-gold btn-petit" href="inscription.php">S'inscrire</a>
            <?php } ?>
        </div>

        <div class="demarrage-carte">
            <div class="etape">2</div>
            <h3>Apprends les bases</h3>
            <p>Regarde les champions disponibles, comprends les synergies et les coûts. Chaque champion est unique !</p>
            <a class="btn btn-border btn-petit" href="personnages.php">Explorer</a>
        </div>

        <div class="demarrage-carte">
            <div class="etape">3</div>
            <h3>Lance une partie</h3>
            <p>Entre en jeu, construis ta composition et affronte 8 adversaires. Deviens le champion et gagne la victoire !</p>
            <a class="btn btn-gold btn-petit" href="jeu.php">Jouer</a>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
