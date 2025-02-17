<?php
// Détection automatique de l'environnement
$isDevServer = (php_sapi_name() === 'cli-server');

// Désactiver les avertissements de dépréciation
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Configuration des chemins
if ($isDevServer) {
    define('BASE_URL', '');
} else {
    define('BASE_URL', '/jamDifus_test');
}

// Autres configurations globales
define('APP_NAME', 'Football Manager Pro');
define('APP_VERSION', '1.0.0');

// Configuration de l'affichage des erreurs
ini_set('display_errors', 1);
?> 