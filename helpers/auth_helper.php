<?php
function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}