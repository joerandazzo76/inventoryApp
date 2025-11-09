<?php
// app/VisionProviders/OpenAIVision.php

class OpenAIVision {
    private string $apiKey;
    private string $model = 'gpt-4o'; // gpt-4o supports vision
    
    public function __construct(?string $apiKey = null) {
        $this->apiKey = $apiKey ?: (getenv('OPENAI_API_KEY') ?: '');
        if (empty($this->apiKey)) {
            throw new Exception('OpenAI API key not configured');
        }
    }
    
    /**
     * Analyze an image and extract item information
     * @param string $imagePath Path to the image file
     * @param string $hint Optional hint about what to look for
     * @return array Extracted information
     */
    public function analyzeImage(string $imagePath, string $hint = ''): array {
        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: {$imagePath}");
        }
        
        // Read and encode image to base64
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);
        $mimeType = mime_content_type($imagePath);
        
        // Build the prompt
        $prompt = $this->buildPrompt($hint);
        
        // Prepare the API request
        $payload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64Image}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 500,
            'temperature' => 0.3
        ];
        
        // Make API call
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("OpenAI API error (HTTP {$httpCode}): " . $response);
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Unexpected API response format');
        }
        
        // Parse the response
        return $this->parseResponse($result['choices'][0]['message']['content']);
    }
    
    private function buildPrompt(string $hint): string {
        $basePrompt = "Analyze this image and extract the following information about the item(s) shown:\n\n";
        $basePrompt .= "1. Item title/name\n";
        $basePrompt .= "2. Brief description\n";
        $basePrompt .= "3. Approximate quantity (count how many items)\n";
        $basePrompt .= "4. Category (e.g., drone parts, gun parts, 3D printing, electronics, etc.)\n";
        $basePrompt .= "5. Any visible product IDs, model numbers, or brand names\n\n";
        
        if ($hint) {
            $basePrompt .= "Additional context: {$hint}\n\n";
        }
        
        $basePrompt .= "Return your response in JSON format with these keys:\n";
        $basePrompt .= "{\n";
        $basePrompt .= '  "title": "item name",'."\n";
        $basePrompt .= '  "description": "brief description",'."\n";
        $basePrompt .= '  "quantity": number,'."\n";
        $basePrompt .= '  "category": "category name",'."\n";
        $basePrompt .= '  "product_id": "model/product number if visible",'."\n";
        $basePrompt .= '  "vendor": "brand name if visible"'."\n";
        $basePrompt .= "}\n\n";
        $basePrompt .= "If any information is not visible or uncertain, use null for that field.";
        
        return $basePrompt;
    }
    
    private function parseResponse(string $content): array {
        // Try to extract JSON from the response
        // Sometimes the model wraps JSON in markdown code blocks
        $content = trim($content);
        
        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }
        
        $data = json_decode($content, true);
        
        if (!is_array($data)) {
            throw new Exception('Failed to parse vision response as JSON');
        }
        
        // Normalize the response
        return [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'quantity' => isset($data['quantity']) ? (int)$data['quantity'] : null,
            'category' => $data['category'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'vendor' => $data['vendor'] ?? null,
        ];
    }
}
