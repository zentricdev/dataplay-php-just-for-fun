.PHONY: lint pint phpstan terminator help

PINT = ./vendor/bin/pint
PHPSTAN = ./vendor/bin/phpstan

lint: pint phpstan composer-validate

composer-validate:
	@composer validate --strict

pint:
	@$(PINT)

phpstan:
	@$(PHPSTAN)

terminator:
	@echo
	@php ./src/Terminator/Command.php
	@echo

payday:
	@echo
	@php ./src/PayDay/Command.php
	@echo

help:
	@echo "+------------------+----------------------------------+"
	@echo "| Command make...  | Description                      |"
	@echo "+------------------+----------------------------------+"
	@echo "| lint             | Run all static analysis tools    |"
	@echo "| pint             | Fix code style (Pint)            |"
	@echo "| phpstan          | Analyze code (Level 9)           |"
	@echo "| composer-validate| Validate composer.json (strict)  |"
	@echo "| terminator       | Execute T-800 mission            |"
	@echo "| payday           | Execute Pay Day example          |"
	@echo "+------------------+----------------------------------+"

# Catch-all para que no de error al pasar argumentos
%:
	@:
