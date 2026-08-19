<?php
class Validator
{
    public static function required(mixed $value): bool
    {
        return trim((string)$value) !== '';
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}