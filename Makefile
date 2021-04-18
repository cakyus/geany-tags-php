build:
	php tags.php
install:
	find . -maxdepth 1 -type f -name '*.tags' \
		-print -exec cp {} $(HOME)/.config/geany/tags/ \;
test:
	find tests/ -type f -name '*.php.tags' -delete
	geany -g tests/constants.php.tags tests/constants.php
	geany -g tests/functions.php.tags tests/functions.php
	geany -g tests/classes.php.tags tests/classes.php
clean:
	find . -maxdepth 1 -type f -name '*.tags' -print -delete
