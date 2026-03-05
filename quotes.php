<?php

declare(strict_types=1);
require_once __DIR__ . '/src/storage.php';
$quotes = array_reverse(loadQuotes());
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Teklifler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h1 class="h4">Kayıtlı Teklifler</h1>
    <a href="index.php" class="btn btn-primary">Yeni Teklif</a>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered bg-white">
      <thead class="table-light"><tr><th>Tarih</th><th>Başlık</th><th>Firma</th><th>İşlem</th></tr></thead>
      <tbody>
      <?php foreach ($quotes as $q): ?>
        <tr>
          <td><?= htmlspecialchars($q['created_at']) ?></td>
          <td><?= htmlspecialchars($q['title']) ?></td>
          <td><?= htmlspecialchars($q['company_name']) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="view_quote.php?id=<?= urlencode($q['id']) ?>">Görüntüle</a>
            <a class="btn btn-sm btn-outline-secondary" href="quote_prices.php?id=<?= urlencode($q['id']) ?>">Düzenle</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
