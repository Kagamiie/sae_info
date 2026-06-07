<?php
session_start();
include "includes/header.php";
?>

<!-- HERO SECTION -->
<section class="page-hero">
    <div class="page-hero-contenu">
        <div class="badge">🏆 Auto-battler TFT en PHP</div>
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
    <div class="page-hero-visuel">
        <div class="visuel-glow"></div>
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

<!-- CTA FINALE -->
<section class="page-cta">
    <div class="cta-contenu">
        <h2>Prêt à relever le défi ?</h2>
        <p>Rejoins des centaines de joueurs et prouve que tu es le meilleur auto-battler du plateau !</p>
        <div class="cta-boutons">
            <a class="btn btn-gold btn-grand" href="jeu.php">⚔️ Lancer une partie</a>
            <?php if (!isset($_SESSION["pseudo"])) { ?>
                <a class="btn btn-border btn-grand" href="inscription.php">👤 Créer un compte</a>
            <?php } ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>

<style>
/* ===== ACCUEIL - CSS SIMPLE ===== */

/* Page Hero */
.page-hero {
    min-height: 500px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    padding: 60px 40px;
    margin: -24px -24px 0;
    border: 1px solid rgba(148, 163, 184, 0.16);
    background: linear-gradient(180deg, rgba(15, 22, 39, 0.95), rgba(11, 15, 26, 0.95));
}

.page-hero-contenu {
    position: relative;
    z-index: 2;
}

.badge {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(139, 92, 246, 0.15);
    border: 1px solid rgba(139, 92, 246, 0.35);
    color: #c4b5fd;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 20px;
}

.page-hero h1 {
    margin: 0 0 20px;
    font-size: clamp(48px, 8vw, 72px);
    font-weight: 900;
    letter-spacing: 1px;
    line-height: 1.1;
    background: linear-gradient(135deg, #ffffff, rgba(253, 230, 138, 0.95));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.sous-titre {
    font-size: 16px;
    color: var(--muted);
    line-height: 1.6;
    margin: 0 0 40px;
    max-width: 520px;
}

.actions {
    display: flex;
    gap: 16px;
    margin-bottom: 50px;
    flex-wrap: wrap;
}

.btn-gold {
    background: linear-gradient(180deg, var(--gold-light), var(--gold)) !important;
    color: #0b0f1a !important;
    border: none;
}

.btn-gold:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn-border {
    border: 2px solid var(--muted);
    background: transparent !important;
    color: var(--text) !important;
}

.btn-border:hover {
    border-color: var(--gold);
    color: var(--gold);
}

.btn-petit {
    padding: 10px 20px !important;
    font-size: 13px !important;
}

.btn-grand {
    padding: 14px 28px !important;
    font-size: 15px !important;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.stat {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.16);
}

.stat-nombre {
    font-size: 32px;
    font-weight: 800;
    color: var(--gold-light);
}

.stat-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 600;
}

.page-hero-visuel {
    position: relative;
    min-height: 300px;
    background: rgba(18, 24, 40, 0.5);
    border: 1px solid rgba(139, 92, 246, 0.2);
}

.visuel-glow {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(34, 211, 238, 0.1));
}

/* Sections Communes */
.page-gameplay,
.page-features,
.page-demarrage,
.page-cta {
    padding: 60px 40px;
    margin: 0 -24px;
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-top: none;
}

.section-titre {
    text-align: center;
    margin-bottom: 50px;
}

.section-titre h2 {
    margin: 0 0 12px;
    font-size: clamp(36px, 6vw, 48px);
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, rgba(253, 230, 138, 0.92));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-titre p {
    font-size: 16px;
    color: var(--muted);
    margin: 0;
}

/* Cartes Gameplay */
.cartes-grille {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.carte {
    position: relative;
    padding: 30px;
    border: 1px solid rgba(148, 163, 184, 0.16);
    background: rgba(18, 24, 40, 0.6);
    transition: all 0.2s ease;
}

.carte:hover {
    background: rgba(18, 24, 40, 0.8);
    border-color: rgba(139, 92, 246, 0.35);
}

.numero {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gold-light), var(--gold));
    color: #0b0f1a;
    font-size: 20px;
    font-weight: 900;
}

.carte h3 {
    margin: 0 0 12px;
    font-size: 18px;
    color: var(--text);
}

.carte p {
    margin: 0;
    color: var(--muted);
    line-height: 1.6;
    font-size: 14px;
}

/* Features */
.features-grille {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.feature {
    padding: 30px;
    border: 1px solid rgba(148, 163, 184, 0.16);
    background: rgba(18, 24, 40, 0.5);
    transition: all 0.2s ease;
}

.feature:hover {
    background: rgba(18, 24, 40, 0.7);
    border-color: rgba(139, 92, 246, 0.35);
}

.icone {
    font-size: 40px;
    margin-bottom: 16px;
    display: block;
}

.feature h3 {
    margin: 0 0 12px;
    font-size: 18px;
    color: var(--gold-light);
}

.feature p {
    margin: 0 0 16px;
    color: var(--muted);
    line-height: 1.6;
    font-size: 14px;
}

.feature ul {
    margin: 0;
    padding-left: 20px;
    list-style: none;
}

.feature li {
    color: var(--muted);
    font-size: 13px;
    margin: 8px 0;
    padding-left: 16px;
    position: relative;
}

.feature li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--gold-light);
    font-weight: 800;
}

/* Demarrage */
.demarrage-grille {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.demarrage-carte {
    padding: 40px 30px;
    border: 1px solid rgba(148, 163, 184, 0.16);
    background: rgba(18, 24, 40, 0.6);
    text-align: center;
    transition: all 0.2s ease;
}

.demarrage-carte:hover {
    background: rgba(18, 24, 40, 0.8);
    border-color: rgba(139, 92, 246, 0.35);
}

.etape {
    width: 50px;
    height: 50px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gold-light), var(--gold));
    color: #0b0f1a;
    font-size: 24px;
    font-weight: 900;
}

.demarrage-carte h3 {
    margin: 0 0 12px;
    color: var(--text);
    font-size: 18px;
}

.demarrage-carte p {
    margin: 0 0 20px;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
}

/* CTA */
.page-cta {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 350px;
    background: linear-gradient(180deg, rgba(15, 22, 39, 0.95), rgba(11, 15, 26, 0.95));
    border-top: 2px solid rgba(139, 92, 246, 0.2);
}

.cta-contenu {
    text-align: center;
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.cta-contenu h2 {
    margin: 0 0 16px;
    font-size: clamp(36px, 6vw, 48px);
    font-weight: 900;
    background: linear-gradient(135deg, #ffffff, rgba(253, 230, 138, 0.92));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cta-contenu p {
    margin: 0 0 30px;
    font-size: 16px;
    color: var(--muted);
    line-height: 1.6;
}

.cta-boutons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Responsive */
@media (max-width: 900px) {
    .page-hero {
        grid-template-columns: 1fr;
        padding: 40px 24px;
        gap: 30px;
    }

    .page-hero-visuel {
        min-height: 200px;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .page-gameplay,
    .page-features,
    .page-demarrage,
    .page-cta {
        padding: 40px 24px;
    }
}

@media (max-width: 640px) {
    .page-hero {
        padding: 30px 16px;
        gap: 20px;
    }

    .page-hero h1 {
        font-size: 36px;
    }

    .sous-titre {
        font-size: 14px;
    }

    .actions {
        flex-direction: column;
    }

    .actions .btn {
        width: 100%;
    }

    .cartes-grille,
    .features-grille,
    .demarrage-grille {
        grid-template-columns: 1fr;
    }

    .cta-boutons {
        flex-direction: column;
    }

    .cta-boutons .btn {
        width: 100%;
    }

    .stats {
        grid-template-columns: 1fr;
    }
}
</style>
