build:
	php $(PWD)/tags.php
install:
	find $(PWD) -maxdepth 1 -type f -name '*.tags' \
		-print -exec cp {} $(HOME)/.config/geany/tags/ \;
clean:
	find $(PWD) -maxdepth 1 -type f -name '*.tags' -print -delete
