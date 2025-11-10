#!/bin/bash

# Deploy script untuk aapanel server
# Jalankan: bash deploy.sh

echo "🚀 Starting deployment..."

# Pull latest changes
echo "📥 Pulling latest changes from Git..."
git pull origin main

# Clear all caches
echo "🧹 Clearing Laravel caches..."
php artisan optimize:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo "✅ Deployment completed!"
echo "🔄 Please refresh your browser with Ctrl+F5"


