phar:
	phar-build -s ./lib/Imagine -S ./lib/autoload.php --phar ./imagine.phar --ns
	@echo
	@echo "Phar generation finished. The Phar archive is in ./imagine.phar."

test:
	phpunit tests/
