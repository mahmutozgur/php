<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/report.php';

$id = (string)($_GET['id'] ?? '');
$quote = findQuote($id);
if ($quote === null) {
    http_response_code(404);
    exit('Teklif bulunamadı.');
}

$totals = quoteTotals($quote);
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="teklif_' . $quote['id'] . '.xls"');

echo "<table border='1'>";
echo '<tr><th colspan="' . (2 + count($quote['vendors'])) . '">' . htmlspecialchars($quote['title']) . '</th></tr>';
echo '<tr><th>Ürün</th><th>Miktar</th>';
foreach ($quote['vendors'] as $vendor) {
    echo '<th>' . htmlspecialchars($vendor) . '</th>';
}
echo '</tr>';

foreach ($quote['products'] as $pIndex => $product) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($product['name']) . '</td>';
    echo '<td>' . number_format((float)$product['qty'], 2, ',', '.') . ' ' . htmlspecialchars($product['unit']) . '</td>';
    foreach ($quote['vendors'] as $vIndex => $_vendor) {
        $unitPrice = (float)($quote['prices'][$vIndex][$pIndex] ?? 0);
        $lineTotal = ((float)$product['qty']) * $unitPrice;
        echo '<td>Birim: ' . number_format($unitPrice, 2, ',', '.') . ' / Toplam: ' . number_format($lineTotal, 2, ',', '.') . '</td>';
    }
    echo '</tr>';
}

echo '<tr><th colspan="2">Genel Toplam</th>';
foreach ($quote['vendors'] as $vIndex => $_vendor) {
    echo '<th>' . number_format((float)($totals[$vIndex] ?? 0), 2, ',', '.') . '</th>';
}
echo '</tr>';
echo '</table>';
