#!/bin/bash

set -e

re='^[0-9]+$'
PURPLE='\033[0;35m'
NC='\033[0m'

#CHECK USER ROOT PRIVILEGES
###########################

if [ "$USER" != "root" ]; then
    echo "You must run this script with the root privileges (sudo)" >&2; exit 1
fi

#PARSING PARAMETER
##################

if [ ! -f installation.dat ]; then
    echo "1" > installation.dat
fi

if [ "$1" = "" ]; then
    PARAM=$(head -c 1 installation.dat)
else
    if ! [[ $1 =~ $re ]] ; then
        echo "Error: Argument '$1' is not a number" >&2; exit 1
    fi
    PARAM=$1
fi

#01 CONFIGURING MySQL:
######################

if [ "$PARAM" -le "1" ]; then

echo -e "\n${PURPLE}01 CONFIGURING MySQL:"
echo -e "=====================${NC}\n"

read -p "Enter your current MySQL root user: " dbUser
read -s -p "Enter your current MySQL root password: " dbPasswd
echo -e "\n"
read -p "Enter a new Mysql username for this application (remember it for this script's second part): " dbNewUser
read -s -p "Enter MySQL password for this application (remember it for this script's second part): " dbNewPasswd
echo -e "\n"
read -p "Enter a new name for your application database (remember it for this script's second part): " dbName

if [ "$dbPasswd" = "" ]; then
    DBCALL="mysql -u$dbUser"
else
    DBCALL="mysql -u$dbUser -p$dbPasswd"
fi

$DBCALL -e "CREATE DATABASE IF NOT EXISTS $dbName;"
$DBCALL -e "CREATE USER '$dbNewUser'@'localhost' IDENTIFIED BY '$dbNewPasswd';"
$DBCALL -e "GRANT ALL PRIVILEGES ON $dbName . * TO '$dbNewUser'@'localhost';"
$DBCALL -e "FLUSH PRIVILEGES;"

echo "2" > installation.dat

else
    echo -e "\n${PURPLE}IGNORING MySQL CONFIGURATION STEP"
    echo -e "==================================${NC}\n"

    echo "2" > installation.dat
fi

#02 INSTALLING DEPENDENCIES:
############################

if [ "$PARAM" -le "2" ]; then

echo -e "\n${PURPLE}02 INSTALLING DEPENDENCIES:"
echo -e "========================${NC}\n"

php composer.phar install

echo "3" > installation.dat

else
    echo -e "\n${PURPLE}IGNORING DEPENDENCIES INSTALLATION STEP"
    echo -e "==================================${NC}\n"

    echo "3" > installation.dat
fi

#03 CONFIGURING SYMFONY & DOCTRINE:
###################################

if [ "$PARAM" -le "3" ]; then

echo -e "\n${PURPLE}03 CONFIGURING SYMFONY & DOCTRINE:"
echo -e "===============================${NC}\n"

php bin/console doctrine:schema:update --force

echo "4" > installation.dat

else
    echo -e "\n${PURPLE}IGNORING SYMFONY & DOCTRINE CONFIGURATION STEP"
    echo -e "==================================${NC}\n"

    echo "4" > installation.dat
fi

#04 CONFIGURING APACHE:
#######################

if [ "$PARAM" -le "4" ]; then

echo -e "\n${PURPLE}04 CONFIGURING APACHE:"
echo -e "===================${NC}\n"

cat vhost1.sample > /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	DocumentRoot $PWD/web" >> /etc/apache2/sites-available/factorio-web-view.local.conf
echo "	<Directory $PWD/web>" >> /etc/apache2/sites-available/factorio-web-view.local.conf
cat vhost2.sample >> /etc/apache2/sites-available/factorio-web-view.local.conf
a2ensite factorio-web-view.local.conf
/etc/init.d/apache2 restart
echo "127.0.0.1		factorio-web-view.local" >> /etc/hosts

echo "5" > installation.dat

else
    echo -e "\n${PURPLE}IGNORING APACHE CONFIGURATION STEP"
    echo -e "==================================${NC}\n"

    echo "5" > installation.dat
fi

#05 CONFIGURING PERMISSIONS:
############################

if [ "$PARAM" -le "5" ]; then

echo -e "\n${PURPLE}05 CONFIGURING PERMISSIONS:"
echo -e "========================${NC}\n"

echo "chown -R www-data:www-data $PWD"
chown -R www-data:www-data $PWD

echo "chmod -R 755 $PWD"
chmod -R 755 $PWD

echo "6" > installation.dat

else
    echo -e "\n${PURPLE}IGNORING PERMISSIONS CONFIGURATION STEP"
    echo -e "==================================${NC}\n"

    echo "6" > installation.dat
fi

#06 CREATING NEW USER:
######################

if [ "$PARAM" -le "6" ]; then

echo -e "\n${PURPLE}06 CREATING NEW USER:"
echo -e "========================${NC}\n"

php bin/console fos:user:create

else
    echo -e "\n${PURPLE}IGNORING USER CREATION STEP"
    echo -e "==================================${NC}\n"
fi

#DONE
#####

echo -e "\n${PURPLE}DONE"
echo -e "====${NC}\n"
