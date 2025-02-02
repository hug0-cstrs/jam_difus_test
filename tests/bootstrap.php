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
    global $pdo;
    $pdo = new PDO(
        'mysql:host=localhost;dbname=football_test;charset=utf8',
        'jamDifus',
        'jam',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return $pdo;
}

// Initialiser la connexion PDO globale pour les tests
global $pdo;
$pdo = getTestDatabaseConnection(); 