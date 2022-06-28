.DEFAULT: build

build:
	php _phpetite/phpetite.php > _site/index.html
	php _phpetite/rss.php > _site/atom.xml

serve: build
	python3 -m http.server --directory _site/
