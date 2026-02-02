#!/bin/bash
set -e

# Wait for lock
wait_for_lock() {
    while sudo fuser /var/lib/dpkg/lock >/dev/null 2>&1; do
        echo "Waiting for apt lock..."
        sleep 5
    done
}

wait_for_lock

# Install PHP 8.4 and modules
echo "Installing/Updating PHP 8.4..."
sudo add-apt-repository -y ppa:ondrej/php
wait_for_lock
sudo apt-get update
wait_for_lock
sudo apt-get install -y nginx mysql-server php8.4 php8.4-fpm php8.4-mysql php8.4-curl php8.4-gd php8.4-mbstring php8.4-xml php8.4-zip php8.4-bcmath php8.4-intl php8.4-cli unzip acl

# Configure Firewall
echo "Configuring Firewall..."
sudo ufw allow 'Nginx Full'
sudo ufw allow 22/tcp
sudo ufw --force enable

# Install Composer
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# Install Node.js
if ! command -v node &> /dev/null; then
    echo "Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    wait_for_lock
    sudo apt-get install -y nodejs
fi

# Create Database & User
echo "Configuring Database..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS furniture_db;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'furniture_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON furniture_db.* TO 'furniture_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Clone/Setup Repo
TARGET_DIR="/var/www/Furniture_api"
if [ ! -d "$TARGET_DIR" ]; then
    echo "Cloning repository..."
    sudo git clone https://github.com/HoeunRaksa/Furniture_api.git "$TARGET_DIR"
fi

# Ensure permissions and git safety
sudo chown -R www-data:www-data "$TARGET_DIR"
sudo git config --system --add safe.directory "$TARGET_DIR"
# Specifically allow www-data to use this directory as well
sudo -u www-data git config --global --add safe.directory "$TARGET_DIR"
sudo chmod -R 775 "$TARGET_DIR/storage" "$TARGET_DIR/bootstrap/cache"

# Nginx Configuration
NGINX_CONF="/etc/nginx/sites-available/furniture_api"
# Always update Nginx config to ensure PHP version and Domain are correct
echo "Configuring Nginx..."
cat <<EOF | sudo tee "$NGINX_CONF"
server {
    listen 80;
    server_name api.furniture.learner-teach.online 54.165.46.134;
    root /var/www/Furniture_api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
sudo ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo systemctl reload nginx

# Project Setup
cd "$TARGET_DIR"
echo "Setting up project..."
if [ ! -f .env ]; then
    sudo -u www-data cp .env.example .env
fi

# Always update .env to ensure correct values
sudo -u www-data sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sudo -u www-data sed -i 's/^# DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sudo -u www-data sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sudo -u www-data sed -i 's/^# DB_PORT=.*/DB_PORT=3306/' .env
sudo -u www-data sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sudo -u www-data sed -i 's/^# DB_DATABASE=.*/DB_DATABASE=furniture_db/' .env
sudo -u www-data sed -i 's/^DB_DATABASE=.*/DB_DATABASE=furniture_db/' .env
sudo -u www-data sed -i 's/^# DB_USERNAME=.*/DB_USERNAME=furniture_user/' .env
sudo -u www-data sed -i 's/^DB_USERNAME=.*/DB_USERNAME=furniture_user/' .env
sudo -u www-data sed -i 's/^# DB_PASSWORD=.*/DB_PASSWORD=StrongPassword123!/' .env
sudo -u www-data sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=StrongPassword123!/' .env

sudo -u www-data sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sudo -u www-data sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env
sudo -u www-data sed -i "s|^APP_URL=.*|APP_URL=https://api.furniture.learner-teach.online|" .env

echo "Installing Dependencies..."
# Set HOME for www-data so composer/npm can write to cache
export HOME=/var/www
sudo -u www-data HOME=/var/www composer install --no-dev --optimize-autoloader

if [ -z "$(sudo -u www-data grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
    sudo -u www-data php artisan key:generate
fi

sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan storage:link

echo "Building Assets..."
sudo mkdir -p /var/www/.npm
sudo chown -R www-data:www-data /var/www/.npm
sudo -u www-data npm install
sudo -u www-data npm run build

echo "Setup Complete!"
