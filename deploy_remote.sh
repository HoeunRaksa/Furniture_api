echo "== COMPOSER SETUP =="
sudo mkdir -p /var/www/.composer /var/www/.config /var/www/.cache
sudo chown -R www-data:www-data /var/www/.composer /var/www/.config /var/www/.cache

echo "== COMPOSER INSTALL =="
cd /var/www/Furniture_api
sudo -u www-data env HOME=/var/www composer install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction

echo "== ENV CHECK & UPDATE =="
if [ ! -f .env ]; then
  sudo -u www-data cp .env.example .env
  sudo -u www-data php artisan key:generate
fi

# Ensure production credentials are set
sudo -u www-data sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sudo -u www-data sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sudo -u www-data sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sudo -u www-data sed -i 's/^DB_DATABASE=.*/DB_DATABASE=furniture_db/' .env
sudo -u www-data sed -i 's/^DB_USERNAME=.*/DB_USERNAME=furniture_user/' .env
sudo -u www-data sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=StrongPassword123!/' .env
sudo -u www-data sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env
sudo -u www-data sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env

echo "== DATABASE USER VERIFICATION =="
sudo mysql -e "CREATE DATABASE IF NOT EXISTS furniture_db;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'furniture_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';"
sudo mysql -e "ALTER USER 'furniture_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON furniture_db.* TO 'furniture_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "== LARAVEL OPTIMIZE =="
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "== RELOAD NGINX =="
sudo systemctl reload nginx

echo "== DEPLOY DONE =="
