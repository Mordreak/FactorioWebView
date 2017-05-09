#!/bin/bash

set -e

PURPLE='\033[0;35m'
NC='\033[0m'

echo -e "\n${PURPLE}INSTALLING DEPENDENCIES:"
echo -e "========================${NC}\n"

php composer.phar install

echo -e "\n${PURPLE}CONFIGURING SYMFONY & DOCTRINE:"
echo -e "===============================${NC}\n"

php bin/console doctrine:schema:update --force

echo -e "\n${PURPLE}CONFIGURING APACHE:"
echo -e "===================${NC}\n"

cat vhost1.sample > /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	DocumentRoot $PWD/web" >> /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	<Directory $PWD/web>" >> /etc/apache2/sites-available/factorio-web-view.local.conf
cat vhost2.sample >> /etc/apache2/sites-available/factorio-web-view.local.conf
a2ensite factorio-web-view.local.conf
/etc/init.d/apache2 restart
echo "127.0.0.1		factorio-web-view.local" >> /etc/hosts

echo -e "\n${PURPLE}CONFIGURING PERMISSIONS:"
echo -e "========================${NC}\n"

echo "chown -R www-data:www-data $PWD"
chown -R www-data:www-data $PWD

echo "chmod -R 755 $PWD"
chmod -R 755 $PWD

echo -e "\n${PURPLE}DONE"
echo -e "====${NC}\n"
