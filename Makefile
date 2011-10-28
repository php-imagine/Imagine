TAG=""

define stub
<?php
Phar::mapPhar();

$$basePath = 'phar://' . __FILE__ . '/';

spl_autoload_register(function($$class) use ($$basePath)
{
    if (0 !== strpos($$class, "Imagine\\\\")) {
        return false;
    }
    $$path = str_replace('\\\\', DIRECTORY_SEPARATOR, substr($$class, 8));
    $$file = $$basePath.$$path.'.php';
    var_dump($$file);
    if (file_exists($$file)) {
        require_once $$file;
        return true;
    }
});

__HALT_COMPILER();
endef

export stub

.PHONY: phar test sphinxdocs clean


phar:
	echo "$$stub" >> .stub
	phar-build -s ./lib/Imagine -S ./.stub --phar ./imagine.phar --ns
	rm -f .stub
	@echo
	@echo "Phar generation finished. The Phar archive is in ./imagine.phar."

test:
	phpunit tests/

sphinxdocs:
	@echo "Making docs"
	git ls-files lib/Imagine | while read line; do DIR=`dirname docs/api$${line/#lib\/Imagine/}`; FILE=docs/api$${line/#lib\/Imagine/}; echo $$DIR; test -d $$DIR || mkdir -p $$DIR && doxphp < $$line | doxphp2sphinx > $${FILE/%.php/.rst}; done

clean:
	git clean -df

release:
	git checkout master
	git merge develop
	git tag $(TAG) -m "release $(TAG)"
	git push
	git push --tags