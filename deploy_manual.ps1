# Manual deployment script for Windows PowerShell
# Run this if GitHub Actions keeps timing out

param(
    [Parameter(Mandatory = $false)]
    [string]$ServerIP = "54.165.46.134",
    
    [Parameter(Mandatory = $false)]
    [string]$KeyPath = "C:\Users\Vathana\.ssh\K3.pem"
)

Write-Host "Deploying to server: $ServerIP" -ForegroundColor Green

# Test connection first
Write-Host "Testing connection..." -ForegroundColor Yellow
$connectionTest = Test-NetConnection -ComputerName $ServerIP -Port 22

if (-not $connectionTest.TcpTestSucceeded) {
    Write-Host "ERROR: Cannot connect to $ServerIP on port 22" -ForegroundColor Red
    Write-Host "Please check AWS Security Group settings" -ForegroundColor Red
    exit 1
}

Write-Host "Connection successful. Running deployment..." -ForegroundColor Green

# SSH and run deployment commands
$deployScript = @"
set -e

echo "== GO TO PROJECT =="
cd /var/www/Furniture_api

echo "== FIX GIT SAFE DIRECTORY =="
sudo git config --global --add safe.directory /var/www/Furniture_api
sudo chown -R www-data:www-data /var/www/Furniture_api

echo "== GIT RESET AND PULL =="
sudo -u www-data git reset --hard
sudo -u www-data git pull origin main

echo "== CLEAR CACHE =="
sudo -u www-data php artisan config:clear || true
sudo -u www-data php artisan cache:clear || true
sudo -u www-data php artisan route:clear || true
sudo -u www-data php artisan view:clear || true

echo "== COMPOSER INSTALL =="
sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction

echo "== NPM BUILD =="
sudo -u www-data npm install
sudo -u www-data npm run build

echo "== LARAVEL OPTIMIZE =="
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "== RELOAD NGINX =="
sudo systemctl reload nginx

echo "== DEPLOY COMPLETE =="
"@

# Execute via SSH
ssh -i $KeyPath -o StrictHostKeyChecking=no ubuntu@$ServerIP $deployScript

Write-Host "Deployment completed successfully!" -ForegroundColor Green