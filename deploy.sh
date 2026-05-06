#!/bin/bash

# DBEDC File Tracker - Deployment Script for Shared Hosting
# This script helps deploy the Laravel application to shared hosting

set -e

echo "=========================================="
echo "DBEDC File Tracker Deployment Script"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
REMOTE_HOST=""
REMOTE_USER=""
REMOTE_PATH=""
LOCAL_PATH="$(pwd)"

# Check if .env exists
if [ ! -f "$LOCAL_PATH/.env" ]; then
    echo -e "${RED}Error: .env file not found${NC}"
    echo "Please create .env file from .env.example"
    exit 1
fi

# Ask for deployment details
echo -e "${YELLOW}Please enter deployment details:${NC}"
read -p "Remote host (e.g., user@hostname): " REMOTE_HOST
read -p "Remote path (e.g., /home/user/public_html): " REMOTE_PATH

if [ -z "$REMOTE_HOST" ] || [ -z "$REMOTE_PATH" ]; then
    echo -e "${RED}Error: Remote host and path are required${NC}"
    exit 1
fi

# Backup remote files
echo -e "${YELLOW}Creating backup of remote files...${NC}"
ssh "$REMOTE_HOST" "cd $REMOTE_PATH && tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz . || true"

# Install dependencies locally
echo -e "${YELLOW}Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Clear and cache configs
echo -e "${YELLOW}Optimizing application...${NC}"
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Create deployment package
echo -e "${YELLOW}Creating deployment package...${NC}"
EXCLUDE="--exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='storage/logs/*' --exclude='storage/framework/cache/*' --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*'"

# Sync files to remote
echo -e "${YELLOW}Syncing files to remote server...${NC}"
rsync -avz $EXCLUDE --delete "$LOCAL_PATH/" "$REMOTE_HOST:$REMOTE_PATH/"

# Set permissions on remote
echo -e "${YELLOW}Setting permissions...${NC}"
ssh "$REMOTE_HOST" "cd $REMOTE_PATH && \
    chmod -R 755 public/ && \
    chmod -R 775 storage/ bootstrap/cache && \
    chmod -R 644 storage/logs/*.log 2>/dev/null || true && \
    chown -R www-data:www-data . 2>/dev/null || true"

# Run migrations on remote
echo -e "${YELLOW}Running migrations on remote...${NC}"
ssh "$REMOTE_HOST" "cd $REMOTE_PATH && php artisan migrate --force"

# Clear cache on remote
echo -e "${YELLOW}Clearing cache on remote...${NC}"
ssh "$REMOTE_HOST" "cd $REMOTE_PATH && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear"

echo -e "${GREEN}=========================================="
echo "Deployment completed successfully!"
echo "==========================================${NC}"
echo ""
echo "Next steps:"
echo "1. Verify the application is working at your domain"
echo "2. Check that scheduled tasks are configured in cPanel cron"
echo "3. Test the application functionality"
echo ""
echo "Cron job example:"
echo "* * * * * cd $REMOTE_PATH && php artisan schedule:run >> /dev/null 2>&1"
