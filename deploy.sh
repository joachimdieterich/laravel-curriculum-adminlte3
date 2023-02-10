# Make sure that your system is up-to-date
# sudo apt-get update

# Clone repo from git
git clone https://github.com/joachimdieterich/laravel-curriculum-adminlte3.git
cd laravel-curriculum-adminlte3

# Install/update composer dependecies
composer install --no-dev

# Build assets using Laravel Mix
cp .env.example .env
