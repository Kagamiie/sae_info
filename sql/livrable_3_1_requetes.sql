-- ==========================================================
-- LIVRABLE 3.1 — Script de requêtes SQL + traces d'exécution
-- Projet : Real X TFT / autochess_tft
-- SGBD : MySQL / MariaDB
--
-- RÈGLES DEMANDÉES (cours) :
-- - Pas de fonctions de gestion NULL non vues en cours
-- - Pas de "?" (placeholders)
--
-- IMPORTANT :
-- - Les sections "TRACE" sont à remplir par toi (copie d'écran phpMyAdmin
--   ou copier/coller du résultat).
-- - Les requêtes CREATE USER / GRANT / SHOW GRANTS nécessitent un compte
--   MySQL admin (ex: root MySQL).
-- ==========================================================

USE autochess_tft;

-- ==========================================================
-- A) REQUÊTES UTILISÉES PAR LE SITE (applicatives)
-- (sans mentionner les fichiers PHP)
-- ==========================================================

-- ----------------------------------------------------------
-- A1) Requête simple (SELECT)
-- Exemple : catalogue des personnages
-- ----------------------------------------------------------
SELECT * FROM personnage ORDER BY cout ASC, nom ASC;
-- TRACE A1 :

-- ----------------------------------------------------------
-- A2) Requête avec données saisies par l'utilisateur (SELECT + WHERE)
-- Exemple : connexion (valeurs d'exemple)
-- ----------------------------------------------------------
SELECT id, pseudo, role, statut
FROM utilisateur
WHERE pseudo='root' AND mot_de_passe='root'
LIMIT 1;
-- TRACE A2 :

-- ----------------------------------------------------------
-- A3) Requête imbriquée (sous-requête)
-- Exemple : personnages plus chers que le coût moyen
-- ----------------------------------------------------------
SELECT id, nom, cout
FROM personnage
WHERE cout > (SELECT AVG(cout) FROM personnage)
ORDER BY cout DESC, nom;
-- TRACE A3 :

-- ----------------------------------------------------------
-- A4) Requête avec agrégation (MIN / MAX / AVG)
-- ----------------------------------------------------------
SELECT
  MIN(pv) AS pv_min,
  MAX(pv) AS pv_max,
  AVG(pv) AS pv_moyen,
  AVG(attaque) AS attaque_moyenne
FROM personnage;
-- TRACE A4 :

-- ----------------------------------------------------------
-- A5) Requête GROUP BY + HAVING
-- Exemple : coûts présents au moins 2 fois
-- ----------------------------------------------------------
SELECT cout, COUNT(*) AS nb_personnages
FROM personnage
GROUP BY cout
HAVING COUNT(*) >= 2
ORDER BY cout;
-- TRACE A5 :

-- ----------------------------------------------------------
-- A6) Requête avec jointure (JOIN)
-- Exemple : utilisateurs + nombre de parties
-- NB : certains utilisateurs peuvent ne pas avoir de parties → nb_parties peut être NULL
-- ----------------------------------------------------------
SELECT
  u.id,
  u.pseudo,
  u.role,
  u.statut,
  p.nb_parties AS nb_parties
FROM utilisateur u
LEFT JOIN (
  SELECT id_utilisateur, COUNT(*) AS nb_parties
  FROM partie
  GROUP BY id_utilisateur
) p ON p.id_utilisateur = u.id
ORDER BY nb_parties DESC, u.id DESC;
-- TRACE A6 :

-- ----------------------------------------------------------
-- A7) Requête de création d’un utilisateur (INSERT)
-- Exemple : inscription (valeurs d'exemple)
-- ----------------------------------------------------------
INSERT INTO utilisateur (pseudo, mot_de_passe, role, statut)
VALUES ('nouveau_joueur', 'mdp123', 'joueur', 'actif');
-- TRACE A7 :

-- ----------------------------------------------------------
-- A8) Requête de mise à jour (UPDATE)
-- Exemple : suspendre / réactiver un compte
-- ----------------------------------------------------------
UPDATE utilisateur SET statut = 'suspendu' WHERE pseudo = 'nouveau_joueur';
UPDATE utilisateur SET statut = 'actif' WHERE pseudo = 'nouveau_joueur';
-- TRACE A8 :

-- ----------------------------------------------------------
-- A9) Requête de suppression (DELETE)
-- Exemple : supprimer une partie puis un utilisateur
-- ----------------------------------------------------------
-- (Adapte les id/pseudos à tes données)
DELETE FROM partie WHERE id_utilisateur = (SELECT id FROM utilisateur WHERE pseudo='nouveau_joueur' LIMIT 1);
DELETE FROM utilisateur WHERE pseudo='nouveau_joueur';
-- TRACE A9 :

-- ==========================================================
-- B) REQUÊTES D’ADMINISTRATION MySQL (privilèges)
-- ==========================================================

-- ----------------------------------------------------------
-- B1) Création d’un utilisateur MySQL
-- ----------------------------------------------------------
CREATE USER 'rx_app'@'localhost' IDENTIFIED BY 'RxApp_2026!';
-- TRACE B1 :

-- ----------------------------------------------------------
-- B2) Attribution des droits (GRANT)
-- ----------------------------------------------------------
GRANT SELECT, INSERT, UPDATE, DELETE ON autochess_tft.* TO 'rx_app'@'localhost';
FLUSH PRIVILEGES;
-- TRACE B2 :

-- ----------------------------------------------------------
-- B3) Visualiser tous les utilisateurs MySQL et leurs privilèges
-- ----------------------------------------------------------
SELECT user, host FROM mysql.user ORDER BY user, host;
SHOW GRANTS FOR 'rx_app'@'localhost';

-- Vues "information_schema" (selon configuration)
SELECT * FROM information_schema.user_privileges;
SELECT * FROM information_schema.schema_privileges WHERE table_schema='autochess_tft';
SELECT * FROM information_schema.table_privileges WHERE table_schema='autochess_tft';
-- TRACE B3 :
