# PHP Teklif Yönetimi

Bu proje; aynı teklif içinde çoklu tedarikçi (7-8 ve üzeri) ve çoklu ürün (20-30+ kalem) girişi yapıp sonucu karşılaştırmalı olarak görüntülemenizi sağlar.

## Özellikler
- Dinamik tedarikçi ekleme/silme
- Dinamik ürün kalemi ekleme/silme
- Ürün x tedarikçi bazında birim fiyat matrisi
- Otomatik toplam hesapları ve en uygun teklif vurgusu
- Excel (.xls) dışa aktarımı
- PDF dışa aktarımı

## Çalıştırma
```bash
php -S 0.0.0.0:8000
```

Ardından `http://localhost:8000/index.php` adresine gidin.

## Sayfalar
- `index.php`: teklif başlığı, tedarikçiler ve ürün kalemleri oluşturma
- `quote_prices.php`: her ürün için tedarikçi bazlı fiyat girişi
- `view_quote.php`: karşılaştırma ekranı + export butonları
- `quotes.php`: tüm teklif kayıtları
- `export_excel.php`: Excel indir
- `export_pdf.php`: PDF indir

Veriler `data/quotes.json` dosyasında tutulur.
