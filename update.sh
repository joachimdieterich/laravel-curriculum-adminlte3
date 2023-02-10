# Turn on maintenance mode
php artisan down || true

# Pull the latest changes from the git repository
# git reset --hard
# git clean -df
git pull

# Install/update composer dependecies
#composer install --no-dev
composer update

# Run database migrations
php artisan migrate --force

# Clear caches/config/routes/views
php artisan clear:all

npm ci

# Build assets using Laravel Mix
npm run production --silent

# Turn off maintenance mode
php artisan up
