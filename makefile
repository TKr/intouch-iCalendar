PHPUNIT := php bin/phpunit -c app/
COMPOSER := php ./composer.phar
GET_COMPOSER := php -r "readfile('https://getcomposer.org/installer');" | php
TEST_COMPOSER := test -e ./composer.phar && $(COMPOSER) --version || $(GET_COMPOSER)

define test_blocks
	$(PHPUNIT) --testsuite=blocks
endef

define test_helpers
	$(PHPUNIT) --testsuite=helpers
endef

help:
	@echo get_composer - download and install or update current version of composer
	@echo dev - download and install dev requirments
	@echo test - run all tests
	@echo test_blocks - run blocks tests
	@echo test_helpers - run helpers tests

get_composer:
	@$(TEST_COMPOSER)
	$(COMPOSER) self-update

test:
	$(call test_blocks)
	$(call test_helpers)

test_blocks:
	$(call test_blocks)

test_helpers:
	$(call test_helpers)

dev:
	@$(TEST_COMPOSER)
	php ./composer.phar install
