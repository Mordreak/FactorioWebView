# FactorioWebView

A Symfony project created on April 28, 2017, 9:14 pm.

FactorioWebView is a fully functional web application to help you manage your Factorio headless server.
It allows you to install the server, start & stop it, create and load saves... And many more incoming features...

# Installation

_Requirements:_
* A 'debian-like' (Ubuntu, Debian, Mint...) Operating System
(As long as this project is not tested/ready for MAC OS X or WINDOWS... (incoming))
* A MySQL server.
* Be root.
* Have PHP and Apache2 installed and working.
* !!IMPORTANT!! : Make sure you have the options 'post_max_size' and 'upload_max_filesize' set to '50M' in your php.ini,
          or you will probably not be able to install the game from the web application.
          (your php.ini file is usually in /etc/php/apache2/php.ini)

_How to:_
* Run the installation script AS ROOT called "installation.sh"
--> "sudo bash installation.sh" make sure you have enough rights to run it. (chmod +x installation.sh)
* Let the installation script guide you trough the steps.
* If the script fails for any reason, try to solve the errors, and run it again, the script knows at which step
the installation failed, and won't try to run again previous configurations.
* If you want to run the installation script again from the beginning, just erase the installation.dat file
* Access http://factorio-web-view.local/config.php and check for warnings or errors to solve.
* You are done! Access your web manager at: http://factorio-web-view.local/
 
_To let other people access your web application:_
* Once the installation.sh script end successfully you can run it again to create new users for your friends
* Edit the VirtualHost in /etc/apache2/site-available/factorio-web-view.local.conf
to make it run with an accessible host-name. (Be root to edit) (example: https://factorio.wittmann.ovh/)
* Restart Apache: "sudo /etc/init.d/apache2 restart"
* Your server is running on port 34197 by default, once installed! (updates are coming soon to let you change it)
