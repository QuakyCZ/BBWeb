up:
	cd docker && docker-compose pull && docker-compose up

up-daemon d:
	cd docker && docker-compose pull && docker-compose up -d

bash b:
	docker exec -it web /bin/bash

wd:
	cd docker && docker-compose run npm npm run build:dev

dc:
	rm -rf temp/cache/*
npm-install:
	cd docker && docker-compose run npm npm install
