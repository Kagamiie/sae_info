REAL X TFT - VERSION 4

IMPORTANT DATABASE
------------------
Si tu as déjà installé la V3 : tu n'as PAS besoin de changer la base de données.
La V4 modifie surtout l'interface de la partie et les animations.

Si tu pars de zéro ou d'une ancienne version : importe sql/database.sql dans phpMyAdmin.

COMPTES
-------
root / root
joueur / joueur

NOUVEAUTES V4
-------------
- Interface de partie refaite pour ressembler davantage à un vrai écran d'auto-battler.
- Plateau centré, propre, sans scroll pendant la partie.
- Scoreboard des joueurs à gauche comme dans un vrai jeu.
- Infos du joueur en haut : PV, niveau, XP, PO, adversaire.
- Synergies et objectif à droite.
- Banc juste sous le plateau.
- Boutique en bas de l'écran.
- Bouton paramètres discret en haut à droite, avec abandon de partie.
- Animations de combat améliorées via CSS.
- Fusions conservées : 3 unités identiques 1 étoile donnent 1 unité 2 étoiles, puis 3 unités 2 étoiles donnent 1 unité 3 étoiles.

INSTALLATION
------------
1. Copier le dossier autochess_tft dans htdocs si tu utilises XAMPP.
2. Créer/importer la base autochess_tft avec sql/database.sql si besoin.
3. Lancer http://localhost/autochess_tft/
4. Se connecter avec root/root pour administrer ou joueur/joueur pour jouer.
