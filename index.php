<?php
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Teklif Oluştur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f7f9fc; }
    .card { border:0; border-radius: 1rem; box-shadow: 0 8px 25px rgba(0,0,0,.06); }
    .table td, .table th { vertical-align: middle; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Fiyat Teklifi Oluştur</h1>
    <a class="btn btn-outline-primary" href="quotes.php">Kayıtlı Teklifler</a>
  </div>

  <div class="card">
    <div class="card-body p-4">
      <form id="quoteForm" action="save_quote.php" method="post">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Talep Eden Firma</label>
            <input required class="form-control" type="text" name="company_name" placeholder="Örn: ABC İnşaat">
          </div>
          <div class="col-md-6">
            <label class="form-label">Teklif Başlığı</label>
            <input required class="form-control" type="text" name="title" placeholder="Örn: Ofis Mobilya Alımı">
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label m-0">Teklif Verecek Kişiler / Firmalar</label>
            <button type="button" id="addVendor" class="btn btn-sm btn-secondary">+ Ekle</button>
          </div>
          <div id="vendors"></div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label m-0">Ürünler / Kalemler</label>
            <button type="button" id="addProduct" class="btn btn-sm btn-secondary">+ Kalem Ekle</button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" id="productsTable">
              <thead class="table-light">
              <tr>
                <th style="min-width:220px">Ürün Adı</th>
                <th style="min-width:100px">Miktar</th>
                <th style="min-width:120px">Birim</th>
                <th style="min-width:140px">İşlem</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label">Not</label>
          <textarea class="form-control" rows="3" name="notes" placeholder="Açıklama, teslim süresi vb."></textarea>
        </div>

        <input type="hidden" name="vendors_json" id="vendors_json">
        <input type="hidden" name="products_json" id="products_json">

        <button class="btn btn-primary" type="submit">Teklifi Kaydet</button>
      </form>
    </div>
  </div>
</div>

<script>
const vendorsWrap = document.getElementById('vendors');
const productsBody = document.querySelector('#productsTable tbody');
const addVendorBtn = document.getElementById('addVendor');
const addProductBtn = document.getElementById('addProduct');

function vendorRow(name = '') {
  const div = document.createElement('div');
  div.className = 'input-group mb-2';
  div.innerHTML = `
    <input type="text" class="form-control vendor-input" placeholder="Örn: Mehmet Yılmaz / XYZ Ltd" value="${name}">
    <button type="button" class="btn btn-outline-danger remove-vendor">Sil</button>
  `;
  div.querySelector('.remove-vendor').addEventListener('click', () => div.remove());
  vendorsWrap.appendChild(div);
}

function productRow(product = {}) {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input required type="text" class="form-control product-name" placeholder="Ürün adı" value="${product.name || ''}"></td>
    <td><input required type="number" min="0" step="0.01" class="form-control product-qty" value="${product.qty || 1}"></td>
    <td><input required type="text" class="form-control product-unit" placeholder="Adet, m2, kg..." value="${product.unit || 'Adet'}"></td>
    <td><button type="button" class="btn btn-outline-danger">Sil</button></td>
  `;
  tr.querySelector('button').addEventListener('click', () => tr.remove());
  productsBody.appendChild(tr);
}

addVendorBtn.addEventListener('click', () => vendorRow(''));
addProductBtn.addEventListener('click', () => productRow({}));

vendorRow('Tedarikçi 1');
productRow({name: 'Örnek Ürün', qty: 1, unit: 'Adet'});

quoteForm.addEventListener('submit', (e) => {
  const vendors = [...document.querySelectorAll('.vendor-input')]
    .map(i => i.value.trim())
    .filter(Boolean);

  const products = [...productsBody.querySelectorAll('tr')].map(tr => ({
    name: tr.querySelector('.product-name').value.trim(),
    qty: parseFloat(tr.querySelector('.product-qty').value || '0'),
    unit: tr.querySelector('.product-unit').value.trim()
  })).filter(p => p.name);

  if (vendors.length === 0 || products.length === 0) {
    e.preventDefault();
    alert('En az 1 tedarikçi ve 1 ürün girmelisiniz.');
    return;
  }

  document.getElementById('vendors_json').value = JSON.stringify(vendors);
  document.getElementById('products_json').value = JSON.stringify(products);
});
</script>
</body>
</html>
