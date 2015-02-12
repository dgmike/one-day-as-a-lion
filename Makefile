.SILENT: help
.PHONY: help composer

help:
	echo 'make help     - show this help'
	echo 'make composer - installs composer'
	echo 'make install  - installs project'
	echo 'make update   - updates project'
	echo 'make server   - run server on port 8080'

check:
	command -v curl >/dev/null 2>&1 || { echo >&2 "Please install curl or set it in your path. Aborting."; exit 1; }
	command -v php >/dev/null 2>&1 || { echo >&2 "Please install php or set it in your path. Aborting."; exit 1; }

composer: check
	if [ ! -f composer.phar ]; then curl -sS https://getcomposer.org/installer | php ; fi

bowerinstall:
	bower install

bowerupdate:
	bower update

composerinstall: composer
	php composer.phar install

composerupdate: composer
	php composer.phar update

install: composerinstall bowerinstall

update: composerupdate bowerupdate

server:
	php -S localhost:8080 app.php
