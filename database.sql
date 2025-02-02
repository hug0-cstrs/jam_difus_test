DROP DATABASE IF EXISTS football;
CREATE DATABASE football;
USE football;

-- Suppression des tables si elles existent
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS players;

-- Création de la table des joueurs
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    team VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    nationality VARCHAR(100) NOT NULL,
    goals_scored INT DEFAULT 0,
    image_url VARCHAR(555) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
