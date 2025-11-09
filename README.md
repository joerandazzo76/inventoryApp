# Inventory App (PHP + SQL Server)

A simple inventory/bin-tracking web app with CRUD for Bins and Items, image upload, and a pluggable "Auto-fill from Image" stub (vision + scraping).

## Features
- Bins CRUD (each bin has a number and optional category label)
- Items CRUD (title, description, vendor, price, product id, URL, quantity, bin)
- Photo upload per item (stored under `public/uploads/`)
- Auto-fill (beta): stub endpoint to (a) suggest bin based on keywords, (b) try to fetch OpenGraph metadata from a provided URL
- Pure PHP (PDO SQLSRV) + simple HTML (Tailwind CDN). No framework.
- Works with Microsoft SQL Server.

## Quick Start

### Option 1: Docker (Recommended)

1. **Clone and setup:**
   ```bash
   git clone https://github.com/joerandazzo76/inventoryApp.git
   cd inventoryApp
   cp .env.example .env
   ```

2. **Start the containers:**
   ```bash
   docker-compose up --build
   ```
   Wait for SQL Server to fully start (30-60 seconds).

3. **Initialize the database:**
   Connect to SQL Server at `localhost:1433` with user `sa` and password `YourStrong!Passw0rd`, then:
   ```sql
   CREATE DATABASE inventory_app;
   GO
   ```
   Run the schema from `sql/schema.sql`.

4. **Access the app:**
   Open http://localhost:8080 in your browser.

### Option 2: Manual Setup

1. **Requirements**
   - PHP 8.1+ with `sqlsrv` and/or `pdo_sqlsrv` extensions enabled
   - Microsoft SQL Server (local or remote)
   - A web server (Apache/Nginx). Point your doc root to `public/`.

2. **Database**
   - Create a database (e.g., `inventory_app`).
   - Run the schema: `sql/schema.sql`
   - Update DB credentials in `app/config.php`.

3. **Serve**
   - Place this folder on your server.
   - Set document root to `public/`.
   - Visit `http://localhost/`.

## Auto-Fill from Image (Vision) – Pluggable
- The app includes a **stub** `VisionController::autoFill()` that:
  - Suggests a bin based on category keyword matching in the file name or a provided hint.
  - If you provide a **Vendor URL**, it fetches Open Graph metadata to prefill Title/Description.
- Hooks are ready in `app/VisionProviders/` to add:
  - `OpenAI`/`Azure` vision calls
  - Local ONNX/CLIP pipeline
- See `VisionController.php` for TODOs.

## Security Notes
- Basic input validation and CSRF-lite tokens are included, but **not production-hardened**.
- If you expose publicly, add auth, rate limits, stricter validation, and CSRF protections.

## Tailwind
We use the CDN per your preference:
```html
<script src="https://cdn.tailwindcss.com"></script>
```

## File Map
- `public/index.php`: dashboard + nav
- `public/bins.php`: CRUD for bins
- `public/items.php`: CRUD for items (includes upload + auto-fill)
- `public/upload.php`: file receiving endpoint
- `app/config.php`: config, category keywords, basic settings
- `app/db.php`: SQL Server PDO connection
- `app/Models/*.php`: simple models
- `app/Controllers/*.php`: page handlers
- `app/VisionProviders/OpenGraphScraper.php`: generic metadata scraper
- `sql/schema.sql`: DB schema

## License
MIT (do what you want, no warranty).
