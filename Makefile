.SILENT: help
.PHONY: help composer

help:
	echo 'make help     - show this help'
	echo 'make composer - installs composer'

composer:
	php -r "readfile('https://getcomposer.org/installer');" | php
