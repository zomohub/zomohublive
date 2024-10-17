#!/bin/bash

echo "Starting set_permissions.sh"

# Set permissions for necessary files and directories
echo "Setting permissions for wo_langs.js"
sudo chmod 777 /var/www/html/nodejs/models/wo_langs.js || { echo "Failed to set permissions for wo_langs.js"; exit 1; }

echo "Setting permissions for sitemap.xml"
sudo chmod 777 /var/www/html/sitemap.xml || { echo "Failed to set permissions for sitemap.xml"; exit 1; }

echo "Setting permissions for sitemap-index.xml"
sudo chmod 777 /var/www/html/sitemap-index.xml || { echo "Failed to set permissions for sitemap-index.xml"; exit 1; }

echo "Setting permissions for themes img folder"
sudo chmod -R 777 /var/www/html/themes/wowonder/img || { echo "Failed to set permissions for themes img folder"; exit 1; }

echo "Setting permissions for ffmpeg folder"
sudo chmod -R 777 /var/www/html/ffmpeg || { echo "Failed to set permissions for ffmpeg folder"; exit 1; }

echo "Setting permissions for xml folder"
sudo chmod -R 777 /var/www/html/xml || { echo "Failed to set permissions for xml folder"; exit 1; }

echo "Setting permissions for cron-job.php"
sudo chmod 777 /var/www/html/cron-job.php || { echo "Failed to set permissions for cron-job.php"; exit 1; }

echo "set_permissions.sh completed successfully"
