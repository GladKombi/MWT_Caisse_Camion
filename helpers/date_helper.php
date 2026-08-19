<?php
function format_date(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}