<?php $csrf = csrf_token();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bins</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-5xl mx-auto p-6">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Bins</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Back</a>
  </div>

  <div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="font-semibold mb-2">Add / Edit Bin</h2>
    <form method="post" class="grid gap-3 md:grid-cols-4">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="id" id="bin_id">
      <label class="block col-span-1">
        <span class="text-sm">Bin Number</span>
        <input name="bin_number" id="bin_number" type="number" required class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block col-span-1">
        <span class="text-sm">Category (optional)</span>
        <input name="category" id="category" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block col-span-2">
        <span class="text-sm">Notes (optional)</span>
        <input name="notes" id="notes" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <div>
        <button class="px-3 py-2 bg-blue-600 text-white rounded">Save</button>
      </div>
    </form>
  </div>

  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-semibold mb-2">All Bins</h2>
    <table class="w-full text-left border">
      <thead><tr class="bg-gray-50">
        <th class="p-2 border">#</th>
        <th class="p-2 border">Category</th>
        <th class="p-2 border">Notes</th>
        <th class="p-2 border">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($list as $b): ?>
        <tr>
          <td class="p-2 border"><?= h($b['bin_number']) ?></td>
          <td class="p-2 border"><?= h($b['category']) ?></td>
          <td class="p-2 border"><?= h($b['notes']) ?></td>
          <td class="p-2 border">
            <form method="post" class="inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
              <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <button class="px-2 py-1 bg-red-600 text-white rounded" onclick="return confirm('Delete bin?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
