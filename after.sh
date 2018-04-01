#!/bin/sh

cd code
echo "Installing cron job..."
(crontab -l ; echo "* * * * * php /home/vagrant/code/artisan schedule:run >> /dev/null 2>&1")| crontab -
echo "Migrating Database..."
php artisan migrate
echo "Syncing Products..."
php artisan sync_items