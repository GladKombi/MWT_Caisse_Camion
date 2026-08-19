<?php
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/core/App.php';
require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Database.php';

try {
    $app = new App();
    $app->run();
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Erreur serveur</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}