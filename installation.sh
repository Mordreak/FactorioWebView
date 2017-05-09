#!/bin/bash

set -e

PURPLE='\033[0;35m'
NC='\033[0m'

echo -e "\n${PURPLE}01 CONFIGURING MySQL:"
echo -e "===============================${NC}\n"

read -p "Enter your current MySQL root user: " dbUser
read -s -p "Enter your current MySQL root password: " dbPasswd
read -p "Enter a new Mysql username for this application (remember it for this script's third part): " dbNewUser
read -s -p "Enter MySQL password for this application (remember it for this script's third part): " dbNewPasswd
read -p "Enter a new name for your application database (remember it for this script's third part): " dbName

if [ "$dbPasswd" = "" ]; then
    DBCALL="mysql -u$dbUser"
else
    DBCALL="mysql -u$dbUser -p$dbPasswd"
fi

$DBCALL -e "CREATE DATABASE IF NOT EXISTS $dbName;"
$DBCALL -e "CREATE USER '$dbNewUser'@'localhost' IDENTIFIED BY '$dbNewPasswd';"
$DBCALL -e "GRANT ALL PRIVILEGES ON $dbName . * TO '$dbNewUser'@'localhost;"
$DBCALL -e "FLUSH PRIVILEGES;"

echo -e "\n${PURPLE}02 INSTALLING DEPENDENCIES:"
echo -e "========================${NC}\n"

php composer.phar install

echo -e "\n${PURPLE}03 CONFIGURING SYMFONY & DOCTRINE:"
echo -e "===============================${NC}\n"

php bin/console doctrine:schema:update --force

echo -e "\n${PURPLE}04 CONFIGURING APACHE:"
echo -e "===================${NC}\n"

cat vhost1.sample > /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	DocumentRoot $PWD/web" >> /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	<Directory $PWD/web>" >> /etc/apache2/sites-available/factorio-web-view.local.conf
cat vhost2.sample >> /etc/apache2/sites-available/factorio-web-view.local.conf
a2ensite factorio-web-view.local.conf
/etc/init.d/apache2 restart
echo "127.0.0.1		factorio-web-view.local" >> /etc/hosts

echo -e "\n${PURPLE}05 CONFIGURING PERMISSIONS:"
echo -e "========================${NC}\n"

echo "chown -R www-data:www-data $PWD"
chown -R www-data:www-data $PWD

echo "chmod -R 755 $PWD"
chmod -R 755 $PWD

echo -e "\n${PURPLE}05 CREATING NEW USER:"
echo -e "========================${NC}\n"

php bin/console fos:user:create

echo -e "\n${PURPLE}DONE"
echo -e "====${NC}\n"
