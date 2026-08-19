<?php

define('APP_NAME', 'Gestion Camion');

// Détecte le dossier d'installation au lieu d'imposer /gestion_camion/public.
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$basePath = preg_replace('#/public/index\.php$#', '', $scriptName);
$basePath = preg_replace('#/index\.php$#', '', (string) $basePath);
$basePath = rtrim((string) $basePath, '/');
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

define('BASE_PATH', $basePath);
define('BASE_URL', $scheme . '://' . $host . BASE_PATH);
define('ROOT_PATH', dirname(__DIR__));
define('VIEW_PATH', ROOT_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');

date_default_timezone_set('Africa/Lubumbashi');
