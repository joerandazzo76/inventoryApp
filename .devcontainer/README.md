# Docker Setup for Inventory App

This directory contains the Dockerfile for the PHP/Apache web server with SQL Server support.

## Prerequisites
- Docker
- Docker Compose

## Setup Instructions

1. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Customize the .env file** if needed (default values should work for local development)

3. **Build and start the containers:**
   ```bash
   docker-compose up --build
   ```

4. **Initialize the database:**
   Once the containers are running, you need to create the database and run the schema:
   
   ```bash
   # Connect to SQL Server (you can use Azure Data Studio, SQL Server Management Studio, or command line)
   # Server: localhost,1433
   # User: sa
   # Password: YourStrong!Passw0rd (or whatever you set in .env)
   
   # Create the database
   CREATE DATABASE inventory_app;
   GO
   
   # Then run the schema from sql/schema.sql
   ```

5. **Access the application:**
   - Web App: http://localhost:8080
   - SQL Server: localhost:1433
   - SQL Workbench: http://localhost:3000

## Troubleshooting

### SQL Server Connection Issues
- Make sure the MSSQL container is fully started (can take 30-60 seconds)
- Check that SA_PASSWORD meets complexity requirements (uppercase, lowercase, numbers, special chars)
- Verify the database `inventory_app` exists

### File Upload Issues
- The uploads directory is created automatically in the container
- Files are stored in `public/uploads/`

## Development

To make changes:
1. Edit files in `app/`, `public/`, or `sql/`
2. Restart the web container: `docker-compose restart web`
3. For code changes, you may need to rebuild: `docker-compose up --build web`
