# Inventory App

A lightweight PHP web application for tracking inventory bins and the items inside them. It exposes a minimal CRUD interface, photo upload per item, and an extendable "auto-fill from image" stub that you can swap for any vision service.

## Features
- **Bins CRUD** – create, update, and archive bins with optional category labels.
- **Items CRUD** – manage title, description, vendor, URL, product ID, price, quantity, and bin assignment.
- **Photo uploads** – each item can store an accompanying image saved under `public/uploads/`.
- **Auto-fill hook** – stub endpoint that can recommend a bin based on keywords and scrape OpenGraph metadata from product URLs.
- **SQL Server support** – built with `PDO_SQLSRV` for compatibility with Microsoft SQL Server instances.

## Tech Stack
- PHP 8.1+
- Microsoft SQL Server
- Tailwind CSS (CDN)
- Docker + Docker Compose for local development

## Getting Started

### 1. Clone and prepare the project
```bash
git clone https://github.com/joerandazzo76/inventoryApp.git
cd inventoryApp
cp .env.example .env
```
Update the `.env` file with your desired SQL Server credentials.

### 2. Launch with Docker (recommended)
```bash
docker-compose up --build
```
The API container listens on port `8080` and SQL Server on `1433`. Wait 30–60 seconds for SQL Server to finish booting before continuing.

### 3. Create the database
Connect to SQL Server using the credentials from `.env` (defaults: user `sa`, password `YourStrong!Passw0rd`). Then run:
```sql
CREATE DATABASE inventory_app;
GO
```
Finally apply the schema from [`sql/schema.sql`](sql/schema.sql).

### 4. Visit the app
Open [http://localhost:8080](http://localhost:8080) in your browser. The homepage lists bins and recent items with navigation to CRUD pages.

## Manual Installation (without Docker)
1. **Requirements**
   - PHP 8.1+ with `sqlsrv` or `pdo_sqlsrv` extensions enabled.
   - Microsoft SQL Server (local or remote).
   - Web server (Apache/Nginx) pointing its document root to the [`public/`](public) directory.
2. **Environment configuration**
   - Copy `.env.example` to `.env` and edit DB credentials.
   - Alternatively adjust connection values directly in [`app/config.php`](app/config.php).
3. **Database**
   - Create a database and run [`sql/schema.sql`](sql/schema.sql).
4. **Serve**
   - Deploy the project files to your server.
   - Ensure uploads directory `public/uploads/` is writable by the web server.
   - Access the site via your server URL.

## Project Structure
```
app/
  Controllers/    # Page controllers (Bins, Items, Upload, Vision)
  Models/         # Simple models wrapping SQL queries
  VisionProviders/ # Extensible metadata/vision providers
  config.php      # App configuration + category keywords
  db.php          # PDO SQLSRV connection helper
public/
  bins.php, items.php, index.php, upload.php  # Entry points
sql/
  schema.sql      # Database schema definition
```

## Extending Auto-Fill
The default `VisionController::autoFill()` implementation uses heuristics to:
- Suggest a bin based on category keywords.
- Pull OpenGraph metadata for the provided vendor URL using [`OpenGraphScraper`](app/VisionProviders/OpenGraphScraper.php).

You can add new providers under `app/VisionProviders/` and wire them in via the controller to call external services like Azure Vision or OpenAI.

## Development Tips
- Keep file permissions open for uploads: `chmod 775 public/uploads`.
- Tailwind CSS is loaded via CDN in the HTML layout (`<script src="https://cdn.tailwindcss.com"></script>`).
- Check server logs in the Docker container with `docker-compose logs -f app`.

## Contributing
1. Fork the repo and create a feature branch.
2. Follow PSR-12 coding style for PHP files.
3. Submit a pull request describing your changes and testing steps.

## License
MIT License. See [`LICENSE`](LICENSE) if present, or treat this project as MIT per original repository.
