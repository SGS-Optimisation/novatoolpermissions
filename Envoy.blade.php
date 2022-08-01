@servers([ 'production' => 'snac-eus2-p-rg', 'staging' => 'mytransfer-d-uw2-rg'])

@task('deploy-staging', ['on' => 'staging'])
    cd /var/www/dagobah
    git pull origin main
    php8.1 /usr/local/bin/composer install
    sudo service php8.1-fpm restart
    php8.1 artisan config:clear
    php8.1 artisan migrate --force
    sudo supervisorctl restart dagobah-queue-worker:
    php artisan cache:warmup
@endtask

@task('deploy-production', ['on' => 'production'])
    cd /var/www/dagobah
    git pull origin production
    composer install
    sudo systemctl restart php80-php-fpm.service
    php80 artisan config:clear
    php80 artisan migrate --force
    sudo /usr/local/bin/supervisorctl restart dagobah-queue-worker:
@endtask
