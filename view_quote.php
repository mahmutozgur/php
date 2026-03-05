<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/report.php';
require_once __DIR__ . '/src/format.php';

$id = (string)($_GET['id'] ?? '');
$quote = findQuote($id);
if ($quote === null) {
    http_response_code(404);
    echo 'Teklif bulunamadı.';
    exit;
}
$totals = quoteTotals($quote);
$bestVendor = bestVendorIndex($totals);
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($quote['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h1 class="h4 mb-0"><?= htmlspecialchars($quote['title']) ?></h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="quotes.php">Listeye Dön</a>
      <a class="btn btn-outline-success" href="export_excel.php?id=<?= urlencode($quote['id']) ?>">Excel İndir</a>
      <a class="btn btn-outline-danger" href="export_pdf.php?id=<?= urlencode($quote['id']) ?>">PDF İndir</a>
    </div>
  </div>

  <div class="card mb-3"><div class="card-body">
      <strong>Firma:</strong> <?= htmlspecialchars($quote['company_name']) ?> <br>
      <strong>Tarih:</strong> <?= htmlspecialchars($quote['created_at']) ?>
      <?php if (!empty($quote['notes'])): ?><br><strong>Not:</strong> <?= nl2br(htmlspecialchars($quote['notes'])) ?><?php endif; ?>
    </div></div>

  <div class="table-responsive">
    <table class="table table-bordered bg-white">
      <thead class="table-light">
      <tr>
        <th>Ürün</th>
        <th>Miktar</th>
        <?php foreach ($quote['vendors'] as $vendor): ?><th><?= htmlspecialchars($vendor) ?></th><?php endforeach; ?>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($quote['products'] as $pIndex => $product): ?>
        <tr>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= formatQuantity((float)$product['qty']) . ' ' . htmlspecialchars($product['unit']) ?></td>
          <?php foreach ($quote['vendors'] as $vIndex => $_vendor):
              $unitPrice = (float)($quote['prices'][$vIndex][$pIndex] ?? 0);
              $lineTotal = ((float)$product['qty']) * $unitPrice;
              ?>
            <td>
              <div>Birim: <?= formatMoney($unitPrice) ?> ₺</div>
              <small class="text-muted">Toplam: <?= formatMoney($lineTotal) ?> ₺</small>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-light">
      <tr>
        <th colspan="2">Genel Toplam</th>
        <?php foreach ($quote['vendors'] as $vIndex => $vendor): ?>
          <th class="<?= $bestVendor === $vIndex ? 'table-success' : '' ?>">
            <?= formatMoney((float)($totals[$vIndex] ?? 0)) ?> ₺
          </th>
        <?php endforeach; ?>
      </tr>
      </tfoot>
    </table>
  </div>
</div>
</body>
</html>
