CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'joueur',
    statut VARCHAR(20) NOT NULL DEFAULT 'actif'
);

CREATE TABLE personnage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    cout INT NOT NULL DEFAULT 1,
    attaque INT NOT NULL DEFAULT 10,
    defense INT NOT NULL DEFAULT 5,
    pv INT NOT NULL DEFAULT 100,
    vitesse INT NOT NULL DEFAULT 1,
    portee INT NOT NULL DEFAULT 1,
    origine VARCHAR(80) NOT NULL DEFAULT 'Libre',
    classe VARCHAR(80) NOT NULL DEFAULT 'Combattant',
    icone VARCHAR(255) DEFAULT 'default.png'
);

CREATE TABLE partie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    tour INT NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id)
);

INSERT INTO utilisateur (pseudo, mot_de_passe, role, statut) VALUES
('root', 'root', 'root', 'actif'),
('joueur', 'joueur', 'joueur', 'actif');

INSERT INTO personnage (nom, cout, attaque, defense, pv, vitesse, portee, origine, classe, icone) VALUES
('Shinobi Blond', 1, 15, 4, 105, 2, 1, 'Shinobi', 'Dueliste', 'default.png'),
('Epeiste Pirate', 1, 16, 5, 115, 1, 1, 'Pirate', 'Bretteur', 'default.png'),
('Chasseur Demon', 2, 20, 6, 125, 2, 1, 'Demon', 'Assassin', 'default.png'),
('Mage aux Flammes', 2, 22, 3, 90, 1, 3, 'Mage', 'Sorcier', 'default.png'),
('Titan Cuirasse', 2, 14, 12, 210, 1, 1, 'Titan', 'Gardien', 'default.png'),
('Alchimiste Metal', 3, 25, 8, 160, 1, 2, 'Alchimiste', 'Inventeur', 'default.png'),
('Saiyan Bleu', 3, 32, 5, 150, 2, 1, 'Saiyan', 'Dueliste', 'default.png'),
('Capitaine Shinigami', 3, 27, 8, 170, 2, 1, 'Shinigami', 'Bretteur', 'default.png'),
('Esper Telekinesiste', 4, 38, 4, 135, 2, 3, 'Esper', 'Sorcier', 'default.png'),
('Roi des Fourmis', 4, 34, 14, 240, 1, 1, 'Chimere', 'Gardien', 'default.png'),
('Dragon Slayer', 5, 48, 10, 230, 2, 1, 'Dragon', 'Assassin', 'default.png'),
('Empereur des Ombres', 5, 52, 8, 210, 2, 3, 'Ombre', 'Sorcier', 'default.png');
