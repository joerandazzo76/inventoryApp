<?php
// public/index.php
require_once(__DIR__ . '/../app/helpers.php');





$csrf = csrf_token();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory App</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Inventory App</h1>
    <nav class="flex gap-3 mb-6">
      <a class="px-3 py-2 bg-white shadow rounded hover:bg-gray-50" href="bins.php">Bins</a>
      <a class="px-3 py-2 bg-white shadow rounded hover:bg-gray-50" href="items.php">Items</a>
      <button id="apiKeyBtn" class="px-3 py-2 bg-blue-500 text-white shadow rounded hover:bg-blue-600">
        Manage API Key
      </button>
    </nav>
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-2">Welcome</h2>
      <p>Use the navigation to manage bins and items.</p>
    </div>

    <!-- API Key Modal -->
    <div id="apiKeyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold mb-4">OpenAI API Key Configuration</h3>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Current Status:</label>
          <p id="apiKeyStatus" class="text-sm text-gray-600"></p>
        </div>
        <div class="mb-4">
          <label for="apiKeyInput" class="block text-sm font-medium text-gray-700 mb-2">API Key:</label>
          <input 
            type="password" 
            id="apiKeyInput" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="sk-proj-..."
          />
        </div>
        <div id="apiKeyMessage" class="mb-4 p-3 rounded hidden"></div>
        <div class="flex gap-3">
          <button 
            id="saveApiKeyBtn" 
            class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Save
          </button>
          <button 
            id="showApiKeyBtn" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
          >
            Show/Hide
          </button>
          <button 
            id="closeModalBtn" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <script>
      const modal = document.getElementById('apiKeyModal');
      const apiKeyBtn = document.getElementById('apiKeyBtn');
      const closeModalBtn = document.getElementById('closeModalBtn');
      const saveApiKeyBtn = document.getElementById('saveApiKeyBtn');
      const showApiKeyBtn = document.getElementById('showApiKeyBtn');
      const apiKeyInput = document.getElementById('apiKeyInput');
      const apiKeyStatus = document.getElementById('apiKeyStatus');
      const apiKeyMessage = document.getElementById('apiKeyMessage');

      // Open modal and load current API key
      apiKeyBtn.addEventListener('click', async () => {
        modal.classList.remove('hidden');
        await loadApiKey();
      });

      // Close modal
      closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        apiKeyMessage.classList.add('hidden');
      });

      // Close modal on background click
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
          apiKeyMessage.classList.add('hidden');
        }
      });

      // Toggle show/hide password
      showApiKeyBtn.addEventListener('click', () => {
        apiKeyInput.type = apiKeyInput.type === 'password' ? 'text' : 'password';
      });

      // Load current API key
      async function loadApiKey() {
        try {
          const response = await fetch('config.php?action=get');
          const data = await response.json();
          
          if (data.success) {
            apiKeyInput.value = data.apiKey;
            apiKeyStatus.textContent = data.isSet 
              ? '✓ API key is configured' 
              : '✗ API key is not set';
            apiKeyStatus.className = data.isSet 
              ? 'text-sm text-green-600 font-semibold' 
              : 'text-sm text-red-600 font-semibold';
          }
        } catch (error) {
          console.error('Error loading API key:', error);
          showMessage('Error loading API key', 'error');
        }
      }

      // Save API key
      saveApiKeyBtn.addEventListener('click', async () => {
        const apiKey = apiKeyInput.value.trim();
        
        if (!apiKey) {
          showMessage('Please enter an API key', 'error');
          return;
        }

        try {
          const response = await fetch('config.php?action=set', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ apiKey })
          });
          
          const data = await response.json();
          
          if (data.success) {
            showMessage(data.message, 'success');
            await loadApiKey();
          } else {
            showMessage(data.message, 'error');
          }
        } catch (error) {
          console.error('Error saving API key:', error);
          showMessage('Error saving API key', 'error');
        }
      });

      function showMessage(message, type) {
        apiKeyMessage.textContent = message;
        apiKeyMessage.className = 'mb-4 p-3 rounded ' + 
          (type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
        apiKeyMessage.classList.remove('hidden');
      }
    </script>
  </div>
</body>
</html>
