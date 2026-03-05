<?php

declare(strict_types=1);

function dataFile(): string
{
    return __DIR__ . '/../data/quotes.json';
}

function loadQuotes(): array
{
    $file = dataFile();
    if (!file_exists($file)) {
        return [];
    }

    $json = file_get_contents($file);
    $data = json_decode($json ?: '[]', true);

    return is_array($data) ? $data : [];
}

function saveQuotes(array $quotes): void
{
    file_put_contents(dataFile(), json_encode(array_values($quotes), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findQuote(string $id): ?array
{
    foreach (loadQuotes() as $quote) {
        if (($quote['id'] ?? '') === $id) {
            return $quote;
        }
    }

    return null;
}

function updateQuote(array $newQuote): void
{
    $quotes = loadQuotes();
    foreach ($quotes as $i => $quote) {
        if (($quote['id'] ?? '') === ($newQuote['id'] ?? '')) {
            $quotes[$i] = $newQuote;
            saveQuotes($quotes);
            return;
        }
    }

    $quotes[] = $newQuote;
    saveQuotes($quotes);
}
