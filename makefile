build:
	docker-compose up --build -d
up:
	docker-compose up

down:
	docker-compose down

reload:
	make down
	make up

install-composer:
	docker exec what-if_php sh -c "php composer install"
