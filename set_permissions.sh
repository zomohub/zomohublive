#!/bin/bash

# Set permissions for the necessary files and directories with sudo
sudo chmod 777 nodejs/models/wo_langs.js
sudo chmod 777 ./sitemap.xml
sudo chmod 777 ./sitemap-index.xml
sudo chmod -R 777 ./themes/wowonder/img  # Recursive permission for img folder in themes
sudo chmod -R 777 ffmpeg                 # Recursive permission for ffmpeg folder
sudo chmod -R 777 xml                    # Recursive permission for the xml folder
sudo chmod 777 cron-job.php              # Make cron-job.php writable
