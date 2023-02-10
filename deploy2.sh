
# Generate app key
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Clear caches/config/routes/views
php artisan clear:all

npm ci

# Build assets using Laravel Mix
npm run production --silent

# Generate oAuth2 clients
php artisan passport:install

# Generate openAPI documentation
php artisan l5-swagger:generate

# Turn off maintenance mode
php artisan up
