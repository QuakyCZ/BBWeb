up:
	cd docker && docker-compose pull && docker-compose up

bash b:
	docker exec -it web /bin/bash

wd:
	cd docker && docker-compose run npm npm run build:dev