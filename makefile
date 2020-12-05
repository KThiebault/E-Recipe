.PHONY: tests vendor
tests: vendor
	make prepare-test
	vendor/bin/simple-phpunit

.PHONY: prepare-test
prepare-test: bin
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists -f --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load -n --env=test

.PHONY: prepare-dev
database-dev: bin
	php bin/console cache:clear --env=dev
	php bin/console doctrine:database:drop --if-exists -f --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load -n --env=dev

code-quality:
	vendor/bin/phpcbf --standard=PSR12 src tests
	vendor/bin/phpcs --standard=PSR12 src tests
