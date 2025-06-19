-- Table principale pour les informations communes
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_pseudo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les simples utilisateurs
CREATE TABLE simples_utilisateurs (
    utilisateur_id INT PRIMARY KEY,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table pour les conducteurs
CREATE TABLE conducteurs (
    utilisateur_id INT PRIMARY KEY,
    plaque VARCHAR(20) NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table pour les administratifs
CREATE TABLE administratifs (
    utilisateur_id INT PRIMARY KEY,
    region VARCHAR(100) NOT NULL,
    departement VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    mairie VARCHAR(100) NOT NULL,
    date_mairie DATE NOT NULL,
    email_mairie VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);