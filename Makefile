.SILENT: help
.PHONY: help composer

help:
	echo 'make help     - show this help'
	echo 'make composer - installs composer'
	echo 'make install  - installs project'

check:
	command -v curl >/dev/null 2>&1 || { echo >&2 "Please install curl or set it in your path. Aborting."; exit 1; }
	command -v php >/dev/null 2>&1 || { echo >&2 "Please install php or set it in your path. Aborting."; exit 1; }

composer: check
	if [ ! -f composer.phar ]; then curl -sS https://getcomposer.org/installer | php ; fi

install: composer
	php composer.phar install

update: composer
	php composer.phar update

server:
	php -S localhost:8080 app.php
