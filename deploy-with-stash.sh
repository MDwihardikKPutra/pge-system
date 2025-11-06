#!/bin/bash

# Deploy script untuk aapanel server dengan stash local changes
# Jalankan: bash deploy-with-stash.sh

echo "🚀 Starting deployment..."

# Stash local changes
echo "📦 Stashing local changes..."
git stash save "Local changes before pull - $(date +%Y-%m-%d_%H:%M:%S)"

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
echo ""
echo "💡 Note: If you need local changes back, use: git stash list && git stash apply"

