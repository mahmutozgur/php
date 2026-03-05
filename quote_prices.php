<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';
require_once __DIR__ . '/src/format.php';

$id = (string)($_GET['id'] ?? $_POST['id'] ?? '');
$quote = findQuote($id);

if ($quote === null) {
    http_response_code(404);
    echo 'Teklif bulunamadı.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendors = array_values(array_filter(array_map('trim', (array)($_POST['vendor_name'] ?? []))));

    $productNames = (array)($_POST['product_name'] ?? []);
    $productQtys = (array)($_POST['product_qty'] ?? []);
    $productUnits = (array)($_POST['product_unit'] ?? []);

    $products = [];
    $maxProducts = max(count($productNames), count($productQtys), count($productUnits));
    for ($i = 0; $i < $maxProducts; $i++) {
        $name = trim((string)($productNames[$i] ?? ''));
        if ($name === '') {
            continue;
        }

        $qty = (float)str_replace(',', '.', (string)($productQtys[$i] ?? '0'));
        $unit = trim((string)($productUnits[$i] ?? 'Adet'));

        $products[] = [
            'name' => $name,
            'qty' => max(0, $qty),
            'unit' => $unit === '' ? 'Adet' : $unit,
        ];
    }

    if ($vendors === [] || $products === []) {
        http_response_code(422);
        echo 'En az 1 tedarikçi ve 1 ürün olmalıdır.';
        exit;
    }

    $prices = [];
    foreach ($vendors as $vIndex => $_vendor) {
        foreach ($products as $pIndex => $_product) {
            $key = 'price_' . $vIndex . '_' . $pIndex;
            $value = (float)str_replace(',', '.', (string)($_POST[$key] ?? '0'));
            $prices[$vIndex][$pIndex] = max(0, $value);
        }
    }

    $quote['vendors'] = $vendors;
    $quote['products'] = $products;
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
  <h1 class="h4 mb-3"><?= htmlspecialchars($quote['title']) ?> - Düzenleme</h1>

  <form method="post" id="editForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($quote['id']) ?>">

    <div class="d-flex gap-2 mb-3">
      <button type="button" id="addVendorBtn" class="btn btn-outline-secondary btn-sm">+ Tedarikçi Ekle</button>
      <button type="button" id="addProductBtn" class="btn btn-outline-secondary btn-sm">+ Ürün Satırı Ekle</button>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm bg-white" id="editTable">
        <thead class="table-light">
        <tr>
          <th style="min-width: 220px">Ürün</th>
          <th style="min-width: 120px">Miktar</th>
          <th style="min-width: 140px">Birim</th>
          <?php foreach ($quote['vendors'] as $vIndex => $vendor): ?>
            <th class="vendor-head" data-v-index="<?= $vIndex ?>" style="min-width: 180px">
              <input class="form-control form-control-sm vendor-name" name="vendor_name[<?= $vIndex ?>]" value="<?= htmlspecialchars((string)$vendor) ?>">
            </th>
          <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($quote['products'] as $pIndex => $product): ?>
          <tr class="product-row" data-p-index="<?= $pIndex ?>">
            <td><input class="form-control form-control-sm product-name" name="product_name[<?= $pIndex ?>]" value="<?= htmlspecialchars((string)$product['name']) ?>"></td>
            <td><input class="form-control form-control-sm product-qty" type="number" min="0" step="0.01" name="product_qty[<?= $pIndex ?>]" value="<?= htmlspecialchars((string)$product['qty']) ?>"></td>
            <td><input class="form-control form-control-sm product-unit" name="product_unit[<?= $pIndex ?>]" value="<?= htmlspecialchars((string)$product['unit']) ?>"></td>
            <?php foreach ($quote['vendors'] as $vIndex => $_vendor): ?>
              <td class="price-cell" data-v-index="<?= $vIndex ?>">
                <input class="form-control form-control-sm price-input" type="number" min="0" step="0.01" name="price_<?= $vIndex ?>_<?= $pIndex ?>" value="<?= htmlspecialchars((string)($quote['prices'][$vIndex][$pIndex] ?? '0')) ?>">
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <button class="btn btn-primary" type="submit">Kaydet ve Teklifi Gör</button>
  </form>
</div>

<script>
const table = document.getElementById('editTable');
const addVendorBtn = document.getElementById('addVendorBtn');
const addProductBtn = document.getElementById('addProductBtn');

function renumberTable() {
  const vendorHeaders = [...table.querySelectorAll('.vendor-head')];
  vendorHeaders.forEach((th, vIndex) => {
    th.dataset.vIndex = String(vIndex);
    th.querySelector('.vendor-name').name = `vendor_name[${vIndex}]`;
  });

  const rows = [...table.querySelectorAll('.product-row')];
  rows.forEach((row, pIndex) => {
    row.dataset.pIndex = String(pIndex);
    row.querySelector('.product-name').name = `product_name[${pIndex}]`;
    row.querySelector('.product-qty').name = `product_qty[${pIndex}]`;
    row.querySelector('.product-unit').name = `product_unit[${pIndex}]`;

    [...row.querySelectorAll('.price-cell')].forEach((cell, vIndex) => {
      cell.dataset.vIndex = String(vIndex);
      cell.querySelector('.price-input').name = `price_${vIndex}_${pIndex}`;
    });
  });
}

function addVendor(defaultName = '') {
  const headerRow = table.tHead.rows[0];
  const th = document.createElement('th');
  th.className = 'vendor-head';
  th.style.minWidth = '180px';
  th.innerHTML = `<input class="form-control form-control-sm vendor-name" value="${defaultName}">`;
  headerRow.appendChild(th);

  [...table.tBodies[0].rows].forEach((row) => {
    const td = document.createElement('td');
    td.className = 'price-cell';
    td.innerHTML = '<input class="form-control form-control-sm price-input" type="number" min="0" step="0.01" value="0">';
    row.appendChild(td);
  });

  renumberTable();
}

function addProduct(product = {name: '', qty: 1, unit: 'Adet'}) {
  const vendorCount = table.querySelectorAll('.vendor-head').length;
  const tr = document.createElement('tr');
  tr.className = 'product-row';

  let priceCells = '';
  for (let i = 0; i < vendorCount; i++) {
    priceCells += '<td class="price-cell"><input class="form-control form-control-sm price-input" type="number" min="0" step="0.01" value="0"></td>';
  }

  tr.innerHTML = `
    <td><input class="form-control form-control-sm product-name" value="${product.name}"></td>
    <td><input class="form-control form-control-sm product-qty" type="number" min="0" step="0.01" value="${product.qty}"></td>
    <td><input class="form-control form-control-sm product-unit" value="${product.unit}"></td>
    ${priceCells}
  `;

  table.tBodies[0].appendChild(tr);
  renumberTable();
}

addVendorBtn.addEventListener('click', () => {
  const next = table.querySelectorAll('.vendor-head').length + 1;
  addVendor(`Tedarikçi ${next}`);
});

addProductBtn.addEventListener('click', () => addProduct());

renumberTable();
</script>
</body>
</html>
