#!/usr/bin/env bash
echo 'Running composer'
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

echo 'Clearing all caches...'
php artisan optimize:clear
 
echo 'Caching config...'
php artisan config:cache
 
echo 'Caching routes...'
php artisan route:cache
 
echo 'Running migrations...'
php artisan migrate --seeder="RoleAndPermissionSeeder" --force

echo 'Running seeding...'
php artisan migrate --seed  --force

