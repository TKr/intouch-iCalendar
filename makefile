PHPUNIT := php bin/phpunit -c app/
COMPOSER := php ./composer.phar
GET_COMPOSER := php -r "readfile('https://getcomposer.org/installer');" | php
TEST_COMPOSER := test -e ./composer.phar && $(COMPOSER) --version || $(GET_COMPOSER)

define test_unit
	$(PHPUNIT) --testsuite=Tests
endef

help:
	@echo get_composer - download and install or update current version of composer
	@echo dev - download and install dev requirments
	@echo test - run all tests

get_composer:
	@$(TEST_COMPOSER)
	$(COMPOSER) self-update

test:
	$(call test_unit)

dev:
	@$(TEST_COMPOSER)
	php ./composer.phar install
