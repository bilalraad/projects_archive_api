#!/bin/sh

#Update the folder permissions
sudo chmod -R 775 storage/ bootstrap/cache/ vendor/

# Installing Composer
sudo yum -y update
yum install php-cli php-zip wget unzip
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer


# Install Composer Dependency
composer install

# Copy .env.example to .env
cp .env.example .env

# Update permission for .env
# This line is here because we will update .env from command line
sudo chmod 777 .env

#Start the installation process
php artisan application:install

# Install node dependency
npm install
