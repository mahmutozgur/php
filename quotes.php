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
  <style>
    body { background: linear-gradient(160deg, #eef4ff, #f8fbff); min-height: 100vh; }
    .page-shell { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem 3rem; }
    .header-card { border: 0; border-radius: 1rem; box-shadow: 0 12px 30px rgba(17, 24, 39, .08); }
    .table-wrap { border: 0; border-radius: 1rem; box-shadow: 0 10px 25px rgba(17, 24, 39, .08); overflow: hidden; }
    .table tbody tr:hover { background: #f4f8ff; }
  </style>
</head>
<body>
<div class="page-shell">
  <div class="card header-card mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <h1 class="h4 mb-1">Kayıtlı Teklifler</h1>
        <p class="text-muted mb-0">Oluşturduğunuz teklifleri görüntüleyin ve düzenleyin.</p>
      </div>
      <a href="index.php" class="btn btn-primary">Yeni Teklif</a>
    </div>
  </div>

  <div class="table-wrap bg-white">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light">
        <tr>
          <th>Tarih</th>
          <th>Başlık</th>
          <th>Firma</th>
          <th>İşlem</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($quotes === []): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">Henüz teklif kaydı yok.</td></tr>
        <?php endif; ?>
        <?php foreach ($quotes as $q): ?>
          <tr>
            <td><span class="badge text-bg-light border"><?= htmlspecialchars($q['created_at']) ?></span></td>
            <td><?= htmlspecialchars($q['title']) ?></td>
            <td><?= htmlspecialchars($q['company_name']) ?></td>
            <td class="d-flex gap-2 flex-wrap">
              <a class="btn btn-sm btn-outline-primary" href="view_quote.php?id=<?= urlencode($q['id']) ?>">Görüntüle</a>
              <a class="btn btn-sm btn-outline-secondary" href="quote_prices.php?id=<?= urlencode($q['id']) ?>">Düzenle</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
