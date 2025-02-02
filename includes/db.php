<?php
// Vérification si PHPUnit est en cours d'exécution
if (!defined('PHPUNIT_RUNNING')) {
    $host = 'localhost';
    $dbname = 'football';
    $username = 'jamDifus';
    $password = 'jam';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} 