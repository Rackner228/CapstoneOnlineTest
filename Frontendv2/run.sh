#!/bin/bash
echo "Starting Development Server..."
# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "PHP could not be found. Please install PHP first."
    exit 1
fi
php -S localhost:8080