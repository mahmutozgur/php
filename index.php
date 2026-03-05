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
    body {
      background: linear-gradient(145deg, #eef4ff 0%, #f9fbff 45%, #f5f7fb 100%);
      min-height: 100vh;
    }
    .page-shell {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1rem 3rem;
    }
    .hero {
      background: linear-gradient(120deg, #0d6efd, #4f8dff);
      color: #fff;
      border-radius: 1rem;
      box-shadow: 0 14px 30px rgba(13, 110, 253, 0.25);
    }
    .content-card {
      border: 0;
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }
    .section-title {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      font-weight: 600;
      color: #1f2937;
    }
    .table thead th {
      white-space: nowrap;
    }
    .table tbody tr:hover {
      background: #f4f8ff;
    }
  </style>
</head>
<body>
<div class="page-shell">
  <div class="hero p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <h1 class="h3 mb-1">Fiyat Teklifi Oluştur</h1>
        <p class="mb-0 opacity-75">Tedarikçileri, ürünleri ve notları tek ekrandan hızlıca hazırlayın.</p>
      </div>
      <a class="btn btn-light fw-semibold" href="quotes.php">Kayıtlı Teklifler</a>
    </div>
  </div>

  <div class="card content-card">
    <div class="card-body p-4 p-lg-5">
      <form id="quoteForm" action="save_quote.php" method="post">
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Talep Eden Firma</label>
            <input required class="form-control form-control-lg" type="text" name="company_name" placeholder="Örn: ABC İnşaat">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Teklif Başlığı</label>
            <input required class="form-control form-control-lg" type="text" name="title" placeholder="Örn: Ofis Mobilya Alımı">
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="section-title">👥 Teklif Verecek Kişiler / Firmalar</div>
            <button type="button" id="addVendor" class="btn btn-sm btn-outline-primary">+ Ekle</button>
          </div>
          <div id="vendors"></div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="section-title">📦 Ürünler / Kalemler</div>
            <button type="button" id="addProduct" class="btn btn-sm btn-outline-primary">+ Kalem Ekle</button>
          </div>
          <div class="table-responsive border rounded-3">
            <table class="table table-bordered align-middle mb-0" id="productsTable">
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
          <label class="form-label fw-semibold">Not</label>
          <textarea class="form-control" rows="3" name="notes" placeholder="Açıklama, teslim süresi vb."></textarea>
        </div>

        <input type="hidden" name="vendors_json" id="vendors_json">
        <input type="hidden" name="products_json" id="products_json">

        <button class="btn btn-primary btn-lg px-4" type="submit">Teklifi Kaydet</button>
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
    <span class="input-group-text bg-white">Tedarikçi</span>
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
