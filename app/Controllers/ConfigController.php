<?php
// app/Controllers/ConfigController.php

class ConfigController {
    
    public function getApiKey() {
        header('Content-Type: application/json');
        
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        
        echo json_encode([
            'success' => true,
            'apiKey' => $apiKey,
            'isSet' => !empty($apiKey)
        ]);
    }
    
    public function setApiKey() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $apiKey = $data['apiKey'] ?? '';
        
        if (empty($apiKey)) {
            echo json_encode([
                'success' => false,
                'message' => 'API key cannot be empty'
            ]);
            return;
        }
        
        // Update .env file
        $envPath = __DIR__ . '/../../.env';
        
        if (!file_exists($envPath)) {
            echo json_encode([
                'success' => false,
                'message' => '.env file not found'
            ]);
            return;
        }
        
        $envContent = file_get_contents($envPath);
        
        // Check if OPENAI_API_KEY exists in .env
        if (preg_match('/^OPENAI_API_KEY=.*$/m', $envContent)) {
            // Replace existing key
            $envContent = preg_replace(
                '/^OPENAI_API_KEY=.*$/m',
                'OPENAI_API_KEY=' . $apiKey,
                $envContent
            );
        } else {
            // Add new key
            $envContent .= "\nOPENAI_API_KEY=" . $apiKey;
        }
        
        if (file_put_contents($envPath, $envContent)) {
            // Set in current environment
            putenv("OPENAI_API_KEY=" . $apiKey);
            $_ENV['OPENAI_API_KEY'] = $apiKey;
            
            echo json_encode([
                'success' => true,
                'message' => 'API key updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to write to .env file'
            ]);
        }
    }
}
