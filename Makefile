.PHONY help

help:
	echo "make composer - installs composer"
	
composer:
	php -r "readfile('https://getcomposer.org/installer');" | php
