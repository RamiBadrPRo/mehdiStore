Installation process :
mv .env.example .env
//setup the db connection
composer install
php artisan key:generate
php artisan migrate
php artisan passport:install