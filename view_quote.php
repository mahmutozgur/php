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
  <style>
    body { background: linear-gradient(155deg, #eef4ff, #f9fbff); min-height: 100vh; }
    .page-shell { max-width: 1300px; margin: 0 auto; padding: 2rem 1rem 3rem; }
    .hero { background: linear-gradient(120deg, #0d6efd, #4f8dff); color: #fff; border-radius: 1rem; box-shadow: 0 14px 30px rgba(13,110,253,.25); }
    .panel { border: 0; border-radius: 1rem; box-shadow: 0 10px 25px rgba(17,24,39,.08); }
    .table tbody tr:hover { background: #f4f8ff; }
  </style>
</head>
<body>
<div class="page-shell">
  <div class="hero p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <h1 class="h4 mb-1"><?= htmlspecialchars($quote['title']) ?></h1>
        <p class="mb-0 opacity-75">Teklif karşılaştırma özeti</p>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-light" href="quotes.php">Listeye Dön</a>
        <a class="btn btn-outline-light" href="quote_prices.php?id=<?= urlencode($quote['id']) ?>">Düzenle</a>
        <a class="btn btn-success" href="export_excel.php?id=<?= urlencode($quote['id']) ?>">Excel İndir</a>
        <a class="btn btn-danger" href="export_pdf.php?id=<?= urlencode($quote['id']) ?>">PDF İndir</a>
      </div>
    </div>
  </div>

  <div class="card panel mb-3">
    <div class="card-body">
      <div class="row g-2">
        <div class="col-md-4"><strong>Firma:</strong> <?= htmlspecialchars($quote['company_name']) ?></div>
        <div class="col-md-4"><strong>Tarih:</strong> <?= htmlspecialchars($quote['created_at']) ?></div>
        <div class="col-md-4"><strong>Tedarikçi Sayısı:</strong> <?= count($quote['vendors']) ?></div>
      </div>
      <?php if (!empty($quote['notes'])): ?>
        <hr>
        <strong>Not:</strong> <?= nl2br(htmlspecialchars($quote['notes'])) ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="card panel">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0 bg-white">
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
                  <div class="fw-semibold">Birim: <?= formatMoney($unitPrice) ?> ₺</div>
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
                <?php if ($bestVendor === $vIndex): ?><span class="badge text-bg-success ms-2">En Uygun</span><?php endif; ?>
              </th>
            <?php endforeach; ?>
          </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
