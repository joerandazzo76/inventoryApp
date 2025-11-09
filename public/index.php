<?php
// public/index.php
require_once __DIR__ . '/../app/helpers.php';





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
    </nav>
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-2">Welcome</h2>
      <p>Use the navigation to manage bins and items.</p>
    </div>
  </div>
</body>
</html>
