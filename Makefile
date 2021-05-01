.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa:
	vendor/bin/linter src tests

phpstan-install:
	mkdir -p temp/phpstan
	composer require -d temp/phpstan phpstan/phpstan:0.9.2
	composer require -d temp/phpstan phpstan/phpstan-nette:0.9

phpstan:
	temp/phpstan/vendor/bin/phpstan analyse -l max -c phpstan.neon src

tests:
	vendor/bin/tester -s -p php --colors 1 -C tests/cases

coverage-clover:
	vendor/bin/tester -s -p php --colors 1 -C --coverage ./coverage.xml --coverage-src ./src tests/cases

coverage-html:
	vendor/bin/tester -s -p php --colors 1 -C --coverage ./coverage.html --coverage-src ./src tests/cases
