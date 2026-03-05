<?php

declare(strict_types=1);

require_once __DIR__ . '/src/storage.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$companyName = trim((string)($_POST['company_name'] ?? ''));
$title = trim((string)($_POST['title'] ?? ''));
$notes = trim((string)($_POST['notes'] ?? ''));
$vendors = json_decode((string)($_POST['vendors_json'] ?? '[]'), true);
$products = json_decode((string)($_POST['products_json'] ?? '[]'), true);

if ($companyName === '' || $title === '' || !is_array($vendors) || !is_array($products) || count($vendors) === 0 || count($products) === 0) {
    http_response_code(422);
    echo 'Eksik veya hatalı veri gönderildi.';
    exit;
}

$normalizedProducts = array_map(static function (array $product): array {
    return [
        'name' => trim((string)($product['name'] ?? '')),
        'qty' => (float)($product['qty'] ?? 0),
        'unit' => trim((string)($product['unit'] ?? 'Adet')),
    ];
}, $products);

$id = uniqid('q_', true);

$quote = [
    'id' => $id,
    'company_name' => $companyName,
    'title' => $title,
    'notes' => $notes,
    'vendors' => array_values(array_filter(array_map('trim', $vendors))),
    'products' => $normalizedProducts,
    'prices' => [],
    'created_at' => date('Y-m-d H:i:s'),
];

updateQuote($quote);
header('Location: quote_prices.php?id=' . urlencode($id));
