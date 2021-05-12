@servers([ 'production' => 'snac-eus2-p-rg', 'staging' => 'mytransfer-d-uw2-rg'])

@task('deploy-staging', ['on' => 'staging'])
    cd /var/www/dagobah
    git pull origin main
    composer install
    sudo service php7.4-fpm restart
    php artisan config:clear
    php artisan migrate --force
    sudo supervisorctl restart dagobah-queue-worker:
    php artisan cache:warmup
@endtask

@task('deploy-production', ['on' => 'production'])
    cd /var/www/dagobah
    git pull origin production
    composer install
    sudo systemctl restart php-fpm.service
    php artisan config:clear
    php artisan migrate --force
    sudo /usr/local/bin/supervisorctl restart dagobah-queue-worker:
@endtask
