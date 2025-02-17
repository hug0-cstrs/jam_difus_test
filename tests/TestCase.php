<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

require_once __DIR__ . '/Mocks/PhpInputStreamMock.php';

class TestCase extends BaseTestCase
{
    protected $pdo;
    private static $phpWrapperRestored = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        global $pdo;
        $this->pdo = $pdo;
        
        // Réinitialiser les variables globales
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Restaurer le wrapper original si nécessaire
        if (in_array('php', stream_get_wrappers()) && !self::$phpWrapperRestored) {
            stream_wrapper_unregister('php');
            stream_wrapper_restore('php');
            self::$phpWrapperRestored = true;
        }
        
        // Réinitialiser les variables globales
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected function setInputStream($content)
    {
        // Restaurer d'abord le wrapper original si nécessaire
        if (in_array('php', stream_get_wrappers())) {
            stream_wrapper_unregister('php');
            self::$phpWrapperRestored = false;
        }
        
        // Enregistrer notre mock wrapper
        stream_wrapper_register('php', PhpInputStreamMock::class);
        PhpInputStreamMock::$content = $content;
    }
} 