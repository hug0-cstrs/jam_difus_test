<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/template_manager.php';
require_once __DIR__ . '/../includes/models/Player.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

// Utiliser la connexion PDO de test si elle existe
if (defined('PHPUNIT_RUNNING')) {
    global $pdo;
}

try {
    $templateManager = TemplateManager::getInstance();
    $query = "SELECT * FROM players WHERE 1=1";
    $params = [];

    // Recherche avec phpQuery
    if (!empty($_GET['search'])) {
        $search = $_GET['search'] . '%';
        $query .= " AND LOWER(name) LIKE LOWER(?)";
        $params[] = $search;
    }

    // Appliquer les filtres avec phpQuery
    if (!empty($_GET['team'])) {
        $query .= " AND team = ?";
        $params[] = $_GET['team'];
    }

    if (!empty($_GET['position'])) {
        $query .= " AND position = ?";
        $params[] = $_GET['position'];
    }

    $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $playersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir les données en objets Player
    $players = array_map(function($data) {
        return new Player($data);
    }, $playersData);

    // Créer le conteneur pour tous les joueurs
    $playersContainer = $templateManager->loadTemplate('components/players_container.html');
    
    foreach ($players as $index => $player) {
        // Modeler la carte du joueur avec le gestionnaire de templates
        $playerCard = $templateManager->modelPlayerCard($player->toArray());
        // Ajouter l'effet de délai AOS
        $playerCard->find('.col-md-4')->attr('data-aos-delay', $index * 100);
        $playersContainer->find('.players-list')->append($playerCard->html());
    }
    
    // Préparer la réponse avec le HTML modelé et les données
    if (defined('PHPUNIT_RUNNING')) {
        // Pour les tests, convertir les objets en tableaux
        $playersArray = array_map(function($player) {
            return $player->toArray();
        }, $players);
        echo json_encode($playersArray);
    } else {
        $response = [
            'success' => true,
            'html' => $playersContainer->html(),
            'players' => array_map(function($player) {
                return $player->toArray();
            }, $players),
            'count' => count($players)
        ];
        echo json_encode($response);
    }
} catch (PDOException $e) {
    error_log('Erreur SQL : ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des joueurs',
        'message' => $e->getMessage()
    ]);
    http_response_code(500);
}