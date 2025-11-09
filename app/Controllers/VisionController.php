<?php
// app/Controllers/VisionController.php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../VisionProviders/OpenGraphScraper.php';
require_once __DIR__ . '/../VisionProviders/OpenAIVision.php';

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

        // (C) Vision model integration using OpenAI GPT-4 Vision
        if ($imagePath && file_exists($imagePath)) {
            try {
                $apiKey = getenv('OPENAI_API_KEY');
                if ($apiKey) {
                    $vision = new OpenAIVision($apiKey);
                    $visionData = $vision->analyzeImage($imagePath, $hint);
                    
                    // Merge vision data with suggestions (vision takes priority if not already set)
                    if (!$suggested['title'] && $visionData['title']) {
                        $suggested['title'] = $visionData['title'];
                    }
                    if (!$suggested['description'] && $visionData['description']) {
                        $suggested['description'] = $visionData['description'];
                    }
                    if (!$suggested['vendor'] && $visionData['vendor']) {
                        $suggested['vendor'] = $visionData['vendor'];
                    }
                    if (!$suggested['product_id'] && $visionData['product_id']) {
                        $suggested['product_id'] = $visionData['product_id'];
                    }
                    if ($visionData['quantity']) {
                        $suggested['quantity'] = $visionData['quantity'];
                    }
                    
                    // Use vision category to refine bin suggestion if not already set
                    if (!$suggested['bin_number_suggestion'] && $visionData['category']) {
                        $category = strtolower($visionData['category']);
                        foreach ($this->config['categories'] as $binNum => $keywords) {
                            foreach ($keywords as $kw) {
                                if ($kw && str_contains($category, $kw)) {
                                    $suggested['bin_number_suggestion'] = $binNum;
                                    break 2;
                                }
                            }
                        }
                    }
                    
                    $suggested['vision_analysis'] = 'completed';
                } else {
                    $suggested['vision_analysis'] = 'skipped - API key not configured';
                }
            } catch (Throwable $e) {
                $suggested['vision_error'] = $e->getMessage();
            }
        }

        echo json_encode($suggested);
    }
}
