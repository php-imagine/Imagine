VERSION=""

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

define package_start
<?xml version="1.0" encoding="UTF-8"?>
<package packagerversion="1.8.0" version="2.0" xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
    http://pear.php.net/dtd/tasks-1.0.xsd
    http://pear.php.net/dtd/package-2.0
    http://pear.php.net/dtd/package-2.0.xsd">
  <name>Imagine</name>
  <channel>avalanche123.github.com/pear</channel>
  <summary>
    PHP 5.3 Object Oriented image manipulation library.
  </summary>
  <description>
    Image manipulation library for PHP 5.3 inspired by Python's PIL and other image libraries.
  </description>
  <lead>
    <name>Bulat Shakirzyanov</name>
    <user>avalanche123</user>
    <email>mallluhuct at gmail.com</email>
    <active>yes</active>
  </lead>
  <date>$(shell date +%Y-%m-%d)</date>
  <time>$(shell date +%H:%M:%S)</time>
  <version>
    <release>$(VERSION)</release>
    <api>$(VERSION)</api>
  </version>
  <stability>
    <release>beta</release>
    <api>beta</api>
  </stability>
  <license uri="http://www.opensource.org/licenses/mit-license.php">MIT</license>
  <notes>-</notes>
  <contents>
    <dir name="/">
endef

define package_end
    </dir>
  </contents>
  <dependencies>
    <required>
      <php>
        <min>5.3.2</min>
      </php>
      <pearinstaller>
        <min>1.4.0</min>
      </pearinstaller>
    </required>
  </dependencies>
  <phprelease />
</package>
endef

export stub package_start package_end

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

package:
	cd lib/; echo "$$package_start" > package.xml; git ls-files | while read line; do echo "      <file md5sum=\"$$(md5 < $$line)\" name=\"$$line\" role=\"php\" />" >> package.xml; done; echo "$$package_end" >> package.xml; pear package; rm -f package.xml; cd ../
	mv lib/Imagine-$(VERSION).tgz .

release:
	make package
	@echo "a new package Imagine-$(VERSION).tgz has been created"
	make phar
	@echo "a new phar distribution has been created"
	git add imagine.phar; git commit -m "update phar distribution for $(VERSION)"
	@echo "phar committed"
	git checkout master; git merge develop
	@echo "develop merged into master"
	git tag v$(VERSION) -m "release v$(VERSION)"
	@echo "tag v$(VERSION) created"
	git push; git push --tags
	@echo "code pushed"