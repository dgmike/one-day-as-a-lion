.SILENT: help
.PHONY: help composer

help:
	echo 'make help     - show this help'
	echo 'make composer - installs composer'

check:
	command -v curl >/dev/null 2>&1 || { echo >&2 "Please install curl or set it in your path. Aborting."; exit 1; }
	command -v php >/dev/null 2>&1 || { echo >&2 "Please install php or set it in your path. Aborting."; exit 1; }

composer: check
	[[ -f composer.phar ]] || curl -sS https://getcomposer.org/installer | php

install: composer
	php composer.phar install
