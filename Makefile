up:
	cd docker && docker-compose pull && docker-compose up

up-daemon d:
	cd docker && docker-compose pull && docker-compose up -d

bash b:
	docker exec -it web /bin/bash

w:
	cd docker && docker-compose run npm npm run watch:dev

wd:
	cd docker && docker-compose run npm npm run build:dev

dc:
	rm -rf temp/cache/*
npm-install:
	cd docker && docker-compose run npm npm install

nb:
	cd docker && docker-compose run npm sh

phpstan ps:
	docker exec -it web vendor/bin/phpstan analyze -c phpstan.neon

phpcsfix csf:
	docker exec -it web vendor/bin/php-cs-fixer fix app
	