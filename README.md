FactorioWebView
===============

A Symfony project created on April 28, 2017, 9:14 pm.

FactorioWebView is a fully functional web application to help you manage your Factorio headless server.
It allows you to install the server, start & stop it, create and load saves... And many more incoming features...

INSTALLATION:
=============

Requirements:
        - A 'debian-like' (Ubuntu, Debian, Mint...) Operating System
          (As long as this project is not tested for MAC OS X... (incoming))
        - A MySQL server.
        - Be root.
        - Have PHP and Apache2 installed and working.
        - !!IMPORTANT!! : Make sure you have the options 'post_max_size' and 'upload_max_filesize' set to '50M' in your php.ini,
          or you will probably not be able to install the game from the web application.
          (your php.ini file is usually in /etc/php/apache2/php.ini)

How to:
        - Create a new user and database on your MySQL server, it will store the users able to access your web application.
        - Run the installation script as root called "installation.sh"
          --> "sudo ./installation.sh" make sure you have enough rights to run it. (chmod +x installation.sh)
        - Let the installation script guide you trough the steps.
        - Access http://factorio-web-view.local/config.php and check for warnings or errors to solve.
        - You are done!
 
To let other people access your web application:
        - Run "php bin/console fos:user:create" in your terminal. (this will create new users able to log in)
        - Edit the VirtualHost in /etc/apache2/site-available/factorio-web-view.local.conf
          to make it run with an accessible host-name. (Be root to edit) (exemple: https://factorio.wittmann.ovh/)
        - Restart Apache: "sudo /etc/init.d/apache2 restart"
