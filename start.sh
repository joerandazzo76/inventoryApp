#!/bin/bash

# Quick start script for Inventory App

echo "==================================="
echo "Inventory App - Quick Start"
echo "==================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

echo ""
echo "Starting Docker containers..."
echo "This may take a few minutes on first run..."
echo ""

# Start containers
docker compose up -d

echo ""
echo "==================================="
echo "Containers started!"
echo "==================================="
echo ""
echo "Please wait 30-60 seconds for SQL Server to fully initialize."
echo ""
echo "Next steps:"
echo "1. Connect to SQL Server at localhost:1433"
echo "   - User: sa"
echo "   - Password: YourStrong!Passw0rd"
echo ""
echo "2. Run the following SQL commands:"
echo "   CREATE DATABASE inventory_app;"
echo "   GO"
echo ""
echo "3. Execute the schema from sql/schema.sql"
echo ""
echo "4. Access the app at http://localhost:8080"
echo ""
echo "To view logs: docker compose logs -f"
echo "To stop: docker compose down"
echo "==================================="
