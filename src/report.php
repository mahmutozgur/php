<?php

declare(strict_types=1);

function quoteTotals(array $quote): array
{
    $totals = [];
    foreach ($quote['vendors'] as $vIndex => $_vendor) {
        $sum = 0.0;
        foreach ($quote['products'] as $pIndex => $product) {
            $unitPrice = (float)($quote['prices'][$vIndex][$pIndex] ?? 0);
            $sum += ((float)$product['qty']) * $unitPrice;
        }
        $totals[$vIndex] = $sum;
    }

    return $totals;
}

function bestVendorIndex(array $totals): ?int
{
    if ($totals === []) {
        return null;
    }

    $min = min($totals);
    foreach ($totals as $index => $total) {
        if ($total === $min) {
            return (int)$index;
        }
    }

    return null;
}
