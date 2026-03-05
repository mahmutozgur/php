<?php

declare(strict_types=1);

function formatMoney(float $value): string
{
    return number_format($value, 2, ',', '.');
}

function formatQuantity(float $value): string
{
    $formatted = number_format($value, 2, ',', '.');
    $formatted = rtrim($formatted, '0');
    return rtrim($formatted, ',');
}
