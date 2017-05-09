FactorioWebView
===============

A Symfony project created on April 28, 2017, 9:14 pm.

FactorioWebView is a fully functional web application to help you manage your Factorio headless server.
It allows you to install the server, start & stop it, create and load saves... And many more incoming features...

INSTALLATION:
=============

Requirements:
	- A MySQL server.
	- Be root.
	- Have PHP and Apache2 installed and working.

How to:
	- Create a new user and database on your MySQL server, it will store the users able to access your web application.
	- Run the installation script as root called "install.sh"
	  --> "sudo ./install.sh" make sure you have enough rights to run it. (chmod +x install.sh)
	- Let the installation script guide you trough the steps.
	- Access http://factorio-web-view.local/
	- You are done!

To go Further:
	- To let other people access your web application:
		- Run "php bin/console fos:user:create" in your terminal.
		- Edit the VirtualHost in /etc/apache2/site-available/factorio-web-view.local.conf
		  to make it run with an accessible host-name. (Be root to edit) (exemple: https://factorio.wittmann.ovh/)
		- Restart Apache: "sudo /etc/init.d/apache2 restart"
	
