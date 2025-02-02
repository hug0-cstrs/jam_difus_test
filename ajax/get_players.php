<?php
require_once __DIR__ . '/../includes/db.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

try {
    $query = "SELECT * FROM players WHERE 1=1";
    $params = [];

    // Recherche
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $query .= " AND LOWER(name) LIKE LOWER(?)";
        $params[] = $search;
    }

    // Appliquer les filtres
    if (!empty($_GET['team'])) {
        $query .= " AND team = ?";
        $params[] = $_GET['team'];
    }

    if (!empty($_GET['position'])) {
        $query .= " AND position = ?";
        $params[] = $_GET['position'];
    }

    $query .= " ORDER BY name ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $players = $stmt->fetchAll();

    echo json_encode($players);
} catch (PDOException $e) {
    error_log('Erreur SQL : ' . $e->getMessage());
    if (!defined('PHPUNIT_RUNNING')) {
        http_response_code(500);
        exit;
    } else {
        echo json_encode(['error' => 'Erreur lors de la récupération des joueurs']);
        return;
    }
}