<?php

// Définir que nous sommes en mode test
define('PHPUNIT_RUNNING', true);

// Chemin de base
define('BASE_PATH', dirname(__DIR__));

// Autoloader pour les classes de test
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists(BASE_PATH . '/' . $file)) {
        require BASE_PATH . '/' . $file;
    }
});

// Inclure les fichiers nécessaires
require_once BASE_PATH . '/includes/db.php';

// Configuration de test
$_SERVER['DOCUMENT_ROOT'] = BASE_PATH;
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

// Créer une connexion de test à la base de données
function getTestDatabaseConnection() {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=football_test;charset=utf8',
            'jamDifus',
            'jam',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données de test : " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Initialiser la connexion PDO globale pour les tests
global $pdo;
try {
    $pdo = getTestDatabaseConnection();
} catch (Exception $e) {
    echo "Erreur lors de l'initialisation de la base de données de test : " . $e->getMessage() . "\n";
    exit(1);
}
?> 