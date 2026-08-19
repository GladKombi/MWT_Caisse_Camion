<?php
function format_money(float $amount, string $currency = 'USD'): string
{
    return number_format($amount, 2, ',', ' ') . ' ' . $currency;
}