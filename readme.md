# My Favorite Appliances

## Setup

Install the dependencies first

    composer install --dev

Vagrant is the easiest way to get started.

Make homestead and `up`!;

    php vendor/bin/homestead make
    vagrant up
    
By default you can access it using `http://192.168.10.10`

MySQL IP:Port: `127.0.0.1:33060`

## Run Data Source Fetcher Manually

By default data source fetcher runs automatically by cron or you can call it manually;

    vagrant ssh -c "cd code; php artisan sync_items"
    
# Run tests

    vagrant ssh -c "cd code; ./vendor/bin/phpunit"
    
**Enjoy!**
