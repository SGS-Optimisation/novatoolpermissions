<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/npm.php';

// Config
set('application', 'Dagobah');
set('repository', 'https://github.com/SGS-Optimisation/dagobah.git');
set('php_fpm_version', '8.1');

add('shared_files', ['.env', 'soketi-config.json']);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('prod')
    ->set('remote_user', 'localadmin')
    ->set('hostname', 'snac-eus2-ext')
    ->set('bin/php', '/usr/bin/php81')
    ->set('bin/npm', '/home/localadmin/.nvm/versions/node/v16.16.0/bin/npm')
    ->set('bin/supervisorctl', '/usr/local/bin/supervisorctl')
    ->set('deploy_path', '/var/www/dagobah-v2')
    ->set('repository', 'github-dagobah:SGS-Optimisation/dagobah.git')
    ->set('branch', function () {
        return input()->getOption('branch') ?: 'production';
    })
    ;

host('staging')
    ->set('remote_user', 'mytransferdev')
    ->set('hostname', 'mytransfer-d-ext')
    ->set('bin/php', '/usr/bin/php8.1')
    ->set('bin/npm', '/home/mytransferdev/.nvm/versions/node/v16.16.0/bin/npm')
    ->set('bin/supervisorctl', 'supervisorctl')
    ->set('deploy_path', '/var/www/dagobah')
    ->set('repository', 'dagobah-git:SGS-Optimisation/dagobah.git')
    ->set('branch', function () {
        return input()->getOption('branch') ?: 'main';
    });

task('js-build', [
    'npm:install',
    'npm:run:prod',
]);

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'supervisor:restart:queue',
    'supervisor:restart:soketi',
    'npm:install',
    'npm:run:prod',
    'deploy:publish',
    'php-fpm:reload',
]);

task('npm:run:prod', function () {
    cd('{{release_or_current_path}}');
    run('{{bin/npm}} run build');
});

task('supervisor:restart:queue', function() {
    run('sudo {{bin/supervisorctl}} restart dagobah-queue-worker:');
});
task('supervisor:restart:soketi', function() {
    run('sudo {{bin/supervisorctl}} restart soketi:');
});

// Hooks

after('deploy:failed', 'deploy:unlock');
