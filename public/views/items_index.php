<?php $csrf = csrf_token();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Items</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    async function uploadImage() {
      const f = document.querySelector('#image_file').files[0];
      if (!f) return;
      const fd = new FormData();
      fd.append('csrf', document.querySelector('input[name=csrf]').value);
      fd.append('file', f);
      const res = await fetch('upload.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.url) {
        document.querySelector('#image_preview').src = data.url;
        document.querySelector('input[name=image_path]').value = data.path;
      } else { alert(data.error || 'Upload failed'); }
    }
    async function autoFill() {
      const fd = new FormData();
      fd.append('action','autofill');
      fd.append('csrf', document.querySelector('input[name=csrf]').value);
      fd.append('image_path', document.querySelector('input[name=image_path]').value);
      fd.append('vendor_url', document.querySelector('input[name=vendor_url]').value);
      fd.append('hint', document.querySelector('input[name=title]').value);
      const res = await fetch('items.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.error) { alert(data.error); return; }
      if (data.title && !document.querySelector('input[name=title]').value) document.querySelector('input[name=title]').value = data.title;
      if (data.description && !document.querySelector('input[name=description]').value) document.querySelector('input[name=description]').value = data.description;
      if (data.vendor && !document.querySelector('input[name=vendor]').value) document.querySelector('input[name=vendor]').value = data.vendor;
      if (data.bin_number_suggestion) {
        const opt = [...document.querySelector('select[name=bin_id]').options].find(o => o.dataset.number == data.bin_number_suggestion);
        if (opt) document.querySelector('select[name=bin_id]').value = opt.value;
      }
    }
  </script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-6xl mx-auto p-6">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Items</h1>
    <a href="index.php" class="text-blue-600 hover:underline">← Back</a>
  </div>

  <div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="font-semibold mb-2">Add / Edit Item</h2>
    <form method="post" class="grid gap-3 md:grid-cols-3">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="id" id="item_id">
      <input type="hidden" name="image_path">

      <label class="block">
        <span class="text-sm">Title</span>
        <input name="title" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block">
        <span class="text-sm">Vendor URL</span>
        <input name="vendor_url" type="url" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block">
        <span class="text-sm">Vendor</span>
        <input name="vendor" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>

      <label class="block md:col-span-2">
        <span class="text-sm">Description</span>
        <input name="description" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block">
        <span class="text-sm">Price</span>
        <input name="price" type="number" step="0.01" class="mt-1 w-full border rounded px-2 py-1">
      </label>

      <label class="block">
        <span class="text-sm">Product ID</span>
        <input name="product_id" type="text" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block">
        <span class="text-sm">Quantity</span>
        <input name="quantity" type="number" value="1" class="mt-1 w-full border rounded px-2 py-1">
      </label>
      <label class="block">
        <span class="text-sm">Bin</span>
        <select name="bin_id" class="mt-1 w-full border rounded px-2 py-1">
          <option value="">— Select Bin —</option>
          <?php foreach ($bins as $b): ?>
            <option value="<?= (int)$b['id'] ?>" data-number="<?= (int)$b['bin_number'] ?>">
              #<?= (int)$b['bin_number'] ?> <?= $b['category'] ? '(' . h($b['category']) . ')' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <div class="md:col-span-3 border-t pt-3">
        <div class="flex gap-4 items-start">
          <div>
            <img id="image_preview" src="" alt="" class="w-40 h-40 object-contain border rounded bg-gray-50">
          </div>
          <div class="flex-1 grid gap-2">
            <input id="image_file" type="file" accept="image/*" onchange="uploadImage()" class="block">
            <div class="flex gap-2">
              <button type="button" onclick="autoFill()" class="px-3 py-2 bg-emerald-600 text-white rounded">Auto-fill (beta)</button>
              <button class="px-3 py-2 bg-blue-600 text-white rounded">Save Item</button>
            </div>
            <p class="text-xs text-gray-500">Auto-fill uses simple heuristics + Open Graph scraping. Vision model integration hook is in code.</p>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="bg-white p-4 rounded shadow">
    <h2 class="font-semibold mb-2">All Items</h2>
    <table class="w-full text-left border">
      <thead><tr class="bg-gray-50">
        <th class="p-2 border">Image</th>
        <th class="p-2 border">Title</th>
        <th class="p-2 border">Vendor</th>
        <th class="p-2 border">Price</th>
        <th class="p-2 border">Qty</th>
        <th class="p-2 border">Bin</th>
        <th class="p-2 border">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($list as $it): ?>
        <tr>
          <td class="p-2 border"><?php if ($it['image_path']): ?><img src="<?= h(str_replace(__DIR__.'/../', '/', $it['image_path'])) ?>" class="w-16 h-16 object-contain"><?php endif; ?></td>
          <td class="p-2 border">
            <div class="font-semibold"><?= h($it['title']) ?></div>
            <div class="text-xs text-gray-500"><?= h($it['description']) ?></div>
            <?php if ($it['vendor_url']): ?><a class="text-xs text-blue-600" href="<?= h($it['vendor_url']) ?>" target="_blank">Link</a><?php endif; ?>
          </td>
          <td class="p-2 border"><?= h($it['vendor']) ?></td>
          <td class="p-2 border"><?= $it['price'] !== null ? number_format((float)$it['price'], 2) : '' ?></td>
          <td class="p-2 border"><?= (int)$it['quantity'] ?></td>
          <td class="p-2 border"><?= $it['bin_number'] ? '#' . (int)$it['bin_number'] : '' ?></td>
          <td class="p-2 border">
            <form method="post" class="inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button class="px-2 py-1 bg-red-600 text-white rounded" onclick="return confirm('Delete item?')">Delete</button>
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
