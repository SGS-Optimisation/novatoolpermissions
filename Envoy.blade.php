@servers(['staging' => 'mytransfer-d-uw2-rg'])

@task('deploy-staging', ['on' => 'staging'])
    cd /var/www/dagobah
    git pull origin main
    composer install
    sudo service php7.4-fpm restart
    php artisan config:clear
    php artisan migrate --force
    sudo supervisorctl restart dagobah-queue-worker:
@endtask
