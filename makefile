build:
	docker-compose up --build -d
up:
	docker-compose up -d
down:
	docker-compose down

reload:
	make down
	make up

install-composer:
	docker exec -w /var/www/what_if what-if_php composer install --no-interaction --prefer-dist --optimize-autoloader

update-composer:
	docker exec -w /var/www/what_if what-if_php composer update --no-interaction

fix-right:
	sudo chmod -R 777 storage

clear-cache:
	docker exec -it what-if_php php artisan optimize