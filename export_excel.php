<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/report.php';
require_once __DIR__ . '/src/format.php';

$id = (string)($_GET['id'] ?? '');
$quote = findQuote($id);
if ($quote === null) {
    http_response_code(404);
    exit('Teklif bulunamadı.');
}

$totals = quoteTotals($quote);

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="teklif_' . $quote['id'] . '.xls"');

echo "\xEF\xBB\xBF";
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #444; padding: 6px; }
    thead th { background: #efefef; }
    .right { text-align: right; }
    .center { text-align: center; }
  </style>
</head>
<body>
<table>
  <tr>
    <th colspan="<?= 2 + (count($quote['vendors']) * 2) ?>"><?= htmlspecialchars($quote['title']) ?></th>
  </tr>
  <tr>
    <td colspan="<?= 2 + (count($quote['vendors']) * 2) ?>"><strong>Firma:</strong> <?= htmlspecialchars($quote['company_name']) ?> | <strong>Tarih:</strong> <?= htmlspecialchars($quote['created_at']) ?></td>
  </tr>
  <thead>
    <tr>
      <th rowspan="2">Ürün</th>
      <th rowspan="2">Miktar</th>
      <?php foreach ($quote['vendors'] as $vendor): ?>
        <th colspan="2" class="center"><?= htmlspecialchars((string)$vendor) ?></th>
      <?php endforeach; ?>
    </tr>
    <tr>
      <?php foreach ($quote['vendors'] as $_vendor): ?>
        <th>Birim Fiyat (₺)</th>
        <th>Satır Toplamı (₺)</th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($quote['products'] as $pIndex => $product): ?>
    <tr>
      <td><?= htmlspecialchars((string)$product['name']) ?></td>
      <td class="right"><?= formatQuantity((float)$product['qty']) . ' ' . htmlspecialchars((string)$product['unit']) ?></td>
      <?php foreach ($quote['vendors'] as $vIndex => $_vendor): ?>
        <?php
        $unitPrice = (float)($quote['prices'][$vIndex][$pIndex] ?? 0);
        $lineTotal = ((float)$product['qty']) * $unitPrice;
        ?>
        <td class="right"><?= formatMoney($unitPrice) ?></td>
        <td class="right"><?= formatMoney($lineTotal) ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="2">Genel Toplam</th>
      <?php foreach ($quote['vendors'] as $vIndex => $_vendor): ?>
        <th class="right" colspan="2"><?= formatMoney((float)($totals[$vIndex] ?? 0)) ?></th>
      <?php endforeach; ?>
    </tr>
  </tfoot>
</table>
</body>
</html>
