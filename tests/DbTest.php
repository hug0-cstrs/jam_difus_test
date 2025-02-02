<?php

use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    private $pdo;

    // Fonction appelée avant chaque test pour initialiser la connexion à la base de données
    protected function setUp(): void
    {
        $this->pdo = getTestDatabaseConnection();
    }

    // Test de la connexion à la base de données
    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    // Test de la connectivité et des permissions de la base de données
    public function testDatabaseConnectivityAndPermissions()
    {
        try {
            $result = $this->pdo->query('SELECT 1');
            $this->assertTrue($result !== false);
        } catch (PDOException $e) {
            $this->fail('La connexion à la base de données a échoué : ' . $e->getMessage());
        }
    }

    // Fonction appelée après chaque test pour fermer la connexion à la base de données
    protected function tearDown(): void
    {
        $this->pdo = null;
    }
} 