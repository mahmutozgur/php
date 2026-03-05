<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';

$id = (string)($_GET['id'] ?? $_POST['id'] ?? '');
$quote = findQuote($id);

if ($quote === null) {
    http_response_code(404);
    echo 'Teklif bulunamadı.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prices = [];
    foreach ($quote['vendors'] as $vIndex => $vendor) {
        foreach ($quote['products'] as $pIndex => $product) {
            $key = 'price_' . $vIndex . '_' . $pIndex;
            $value = (float)str_replace(',', '.', (string)($_POST[$key] ?? '0'));
            $prices[$vIndex][$pIndex] = max(0, $value);
        }
    }

    $quote['prices'] = $prices;
    updateQuote($quote);
    header('Location: view_quote.php?id=' . urlencode($quote['id']));
    exit;
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fiyat Girişi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="h4 mb-3"><?= htmlspecialchars($quote['title']) ?> - Fiyat Girişi</h1>
  <form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($quote['id']) ?>">
    <div class="table-responsive">
      <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
          <tr>
            <th>Ürün</th>
            <th>Miktar</th>
            <?php foreach ($quote['vendors'] as $vendor): ?>
              <th><?= htmlspecialchars($vendor) ?> (Birim Fiyat)</th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($quote['products'] as $pIndex => $product): ?>
          <tr>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= number_format((float)$product['qty'], 2, ',', '.') . ' ' . htmlspecialchars($product['unit']) ?></td>
            <?php foreach ($quote['vendors'] as $vIndex => $_vendor): ?>
              <td>
                <input class="form-control form-control-sm" type="number" min="0" step="0.01"
                       name="price_<?= $vIndex ?>_<?= $pIndex ?>"
                       value="<?= htmlspecialchars((string)($quote['prices'][$vIndex][$pIndex] ?? '0')) ?>">
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <button class="btn btn-primary" type="submit">Fiyatları Kaydet</button>
  </form>
</div>
</body>
</html>
