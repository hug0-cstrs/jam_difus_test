<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/phpquery_adapter.php';

if (!defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    $pq = PhpQueryAdapter::getInstance();
    
    // Récupérer et valider les données avec phpQuery
    $data = json_decode(file_get_contents('php://input'), true);
    $formContent = $pq->wrapContent('<form></form>');
    
    // Validation de l'ID
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        echo json_encode(['error' => "ID du joueur invalide"]);
        exit;
    }
    
    // Validation des champs requis
    $requiredFields = ['name', 'team', 'position', 'age', 'nationality'];
    foreach ($requiredFields as $field) {
        $input = $pq->wrapContent('<input>');
        $input->find('input')
            ->attr('name', $field)
            ->attr('value', $data[$field] ?? '');
            
        if (empty($data[$field])) {
            echo json_encode(['error' => "Le champ {$field} est requis"]);
            exit;
        }
        
        $formContent->append($input);
    }
    
    // Validation de l'âge
    if (!is_numeric($data['age']) || $data['age'] < 15 || $data['age'] > 45) {
        echo json_encode(['error' => "L'âge doit être compris entre 15 et 45 ans"]);
        exit;
    }

    // Vérifier si le joueur existe
    $checkStmt = $pdo->prepare("SELECT id FROM players WHERE id = ?");
    $checkStmt->execute([$data['id']]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['error' => "Joueur non trouvé"]);
        exit;
    }

    // Préparation de la requête
    $query = "UPDATE players SET name = ?, team = ?, position = ?, age = ?, nationality = ?, goals_scored = ?, image_url = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    
    // Exécution de la requête
    $stmt->execute([
        $data['name'],
        $data['team'],
        $data['position'],
        $data['age'],
        $data['nationality'],
        $data['goals_scored'],
        $data['image_url'] ?? null,
        $data['id']
    ]);
    
    // Charger le template de carte joueur pour le joueur mis à jour
    $playerCardTemplate = file_get_contents(__DIR__ . '/../templates/components/player_card.html');
    $playerCard = $pq->wrapContent($playerCardTemplate);
    
    // Modeler la carte avec phpQuery
    $playerCard->find('.player-name')->text($data['name']);
    $playerCard->find('.player-team')->text($data['team']);
    $playerCard->find('.player-position')->text($data['position']);
    
    // Gérer l'image du joueur
    if (!empty($data['image_url'])) {
        $playerCard->find('.player-image')->html(
            '<img src="' . $data['image_url'] . '" alt="' . $data['name'] . '" class="img-fluid rounded">'
        );
    } else {
        $playerCard->find('.player-image')->html(
            '<div class="player-avatar">' . substr($data['name'], 0, 1) . '</div>'
        );
    }
    
    // Ajouter les attributs data
    $playerCard->find('.player-card')
        ->attr('data-id', $data['id'])
        ->attr('data-name', $data['name'])
        ->attr('data-team', $data['team'])
        ->attr('data-position', $data['position']);
    
    // Préparer la réponse
    $response = [
        'success' => true,
        'message' => 'Joueur mis à jour avec succès',
        'player' => $data,
        'html' => $playerCard->html()
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log('Erreur SQL : ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour du joueur']);
    exit;
} 