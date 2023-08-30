# Put the .env file in a directory along with this file.
# Curriculum is installed in a subfolder 'laravel-curriculum-adminlte3'.
# Make sure that the DB is created and the .env file is filled correctly.

# Make sure that your system is up-to-date
# sudo apt-get update

# Clone repo from git
git clone https://github.com/joachimdieterich/laravel-curriculum-adminlte3.git
cp .env laravel-curriculum-adminlte3/.env
cd laravel-curriculum-adminlte3

# Install/update composer dependecies
rm composer.lock
composer install

# Build assets using Laravel Mix
#cp .env.example .env

# Generate app key
php artisan key:generate

# Run database migrations
php artisan migrate --seed --force

# Clear caches/config/routes/views
php artisan clear:all

npm ci

# Build assets using Laravel Mix
npm run production --silent

# Generate oAuth2 clients
php artisan passport:install

# Generate openAPI documentation
php artisan l5-swagger:generate

sudo php artisan websocket:serve
