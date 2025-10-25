<?php
// app/Controllers/VisionController.php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../VisionProviders/OpenGraphScraper.php';

class VisionController {
    private PDO $db;
    private array $config;
    public function __construct(PDO $db) {
        $this->db = $db;
        $this->config = require __DIR__ . '/../config.php';
    }

    // Simple heuristic "auto fill" endpoint:
    // POST: image_path, vendor_url (optional), hint (optional)
    // Returns JSON with suggested fields.
    public function autoFill() {
        header('Content-Type: application/json');
        if (!csrf_check($_POST['csrf'] ?? '')) {
            echo json_encode(['error' => 'Bad CSRF']);
            return;
        }
        $imagePath = trim((string)($_POST['image_path'] ?? ''));
        $vendorUrl = trim((string)($_POST['vendor_url'] ?? ''));
        $hint = strtolower(trim((string)($_POST['hint'] ?? '')));

        $suggested = [
            'title' => null,
            'description' => null,
            'vendor' => null,
            'price' => null,
            'product_id' => null,
            'vendor_url' => $vendorUrl ?: null,
            'quantity' => null,
            'bin_number_suggestion' => null,
        ];

        // (A) Suggest bin based on filename/hint/category keywords
        $hay = strtolower($imagePath . ' ' . $hint);
        foreach ($this->config['categories'] as $binNum => $keywords) {
            foreach ($keywords as $kw) {
                if ($kw && str_contains($hay, $kw)) {
                    $suggested['bin_number_suggestion'] = $binNum;
                    break 2;
                }
            }
        }

        // (B) If vendor URL is provided, fetch metadata
        if ($vendorUrl) {
            try {
                $og = (new OpenGraphScraper())->fetch($vendorUrl);
                if (!empty($og['og:title'])) $suggested['title'] = $og['og:title'];
                if (!empty($og['og:description'])) $suggested['description'] = $og['og:description'];
                if (!empty($og['og:site_name'])) $suggested['vendor'] = $og['og:site_name'];
                // price/product id parsing is site-specific; left as TODO.
            } catch (Throwable $e) {
                $suggested['scrape_error'] = $e->getMessage();
            }
        }

        // TODO: (C) Vision model integration (OpenAI/Azure/local) to recognize item, count quantity, etc.
        // Hook here. You could POST image bytes to a Python microservice, or call an API.

        echo json_encode($suggested);
    }
}
