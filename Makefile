SHELL := /bin/bash

phar:
	phar-build -s ./lib/Imagine -S ./lib/stub.php --phar ./imagine.phar --ns
	@echo
	@echo "Phar generation finished. The Phar archive is in ./imagine.phar."

test:
	phpunit tests/

sphinxdocs:
	@echo "Making docs"
	git ls-files lib/Imagine | while read line; do DIR=`dirname docs/api$${line/#lib\/Imagine/}`; FILE=docs/api$${line/#lib\/Imagine/}; echo $$DIR; test -d $$DIR || mkdir -p $$DIR && doxphp < $$line | doxphp2sphinx > $${FILE/%.php/.rst}; done
