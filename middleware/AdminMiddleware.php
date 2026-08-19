<?php
class AdminMiddleware
{
    public static function handle(): void
    {
        if (($_SESSION['user']['role'] ?? '') !== 'ADMIN') {
            http_response_code(403);
            require dirname(__DIR__) . '/views/errors/403.php';
            exit;
        }
    }
}