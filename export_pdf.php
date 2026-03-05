<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/report.php';
require_once __DIR__ . '/src/simple_pdf.php';

$id = (string)($_GET['id'] ?? '');
$quote = findQuote($id);
if ($quote === null) {
    http_response_code(404);
    exit('Teklif bulunamadı.');
}

$totals = quoteTotals($quote);
$lines = [];
$lines[] = 'Teklif Raporu: ' . $quote['title'];
$lines[] = 'Firma: ' . $quote['company_name'];
$lines[] = 'Tarih: ' . $quote['created_at'];
$lines[] = str_repeat('-', 85);

foreach ($quote['products'] as $pIndex => $product) {
    $lines[] = sprintf('%s | %.2f %s', $product['name'], (float)$product['qty'], $product['unit']);
    foreach ($quote['vendors'] as $vIndex => $vendor) {
        $unitPrice = (float)($quote['prices'][$vIndex][$pIndex] ?? 0);
        $lineTotal = ((float)$product['qty']) * $unitPrice;
        $lines[] = sprintf('   - %s: Birim %.2f TL / Toplam %.2f TL', $vendor, $unitPrice, $lineTotal);
    }
}

$lines[] = str_repeat('-', 85);
$lines[] = 'Genel Toplamlar:';
foreach ($quote['vendors'] as $vIndex => $vendor) {
    $lines[] = sprintf('%s => %.2f TL', $vendor, (float)($totals[$vIndex] ?? 0));
}

$pdf = renderSimplePdf($lines);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="teklif_' . $quote['id'] . '.pdf"');
header('Content-Length: ' . strlen($pdf));

echo $pdf;
