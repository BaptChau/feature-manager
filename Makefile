SHELL := /bin/bash

test-unit:
	vendor/bin/phpunit test

validate-flags:
	bin/validate-flags
