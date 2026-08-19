<?php
class ComptableMiddleware
{
    public static function handle(): void
    {
        $roles = ['ADMIN', 'COMPTABLE'];
        if (!in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
            http_response_code(403);
            require dirname(__DIR__) . '/views/errors/403.php';
            exit;
        }
    }
}