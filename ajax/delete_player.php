<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Vérification de la méthode de requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}
// Vérification de l'ID du joueur
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du joueur manquant ou invalide']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
    $result = $stmt->execute([$_POST['id']]);

    // Vérification du résultat de la suppression
    if ($result && $stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Joueur supprimé avec succès']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Joueur non trouvé']);
    }
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression du joueur']);
}