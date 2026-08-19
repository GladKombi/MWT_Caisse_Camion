<?php
class RoleMiddleware
{
    public static function check(array $roles): bool
    {
        return in_array($_SESSION['user']['role'] ?? null, $roles, true);
    }
}