.SILENT: help
.PHONY: help composer

help:
	echo 'make help     - show this help'
	echo 'make composer - installs composer'

composer:
	command -v curl >/dev/null 2>&1 || { echo >&2 "Please install curl or set it in your path. Aborting."; exit 1; }
	curl -sS https://getcomposer.org/installer | php
