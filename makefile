include .env

build:
	docker-compose up --build -d
up:
	docker-compose up -d
down:
	docker-compose down

reload:
	make down
	make up

db-restore:
	gunzip -c dumps/what_if.sql.gz | docker exec -i what-if_mysql mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE);
db-export:
	docker exec what-if_mysql mysqldump -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) | gzip > dumps/what_if_backup_$(shell date +%F).sql.gz

install-composer:
	docker exec -w /var/www/what_if what-if_php composer install --no-interaction --prefer-dist --optimize-autoloader
update-composer:
	docker exec -w /var/www/what_if what-if_php composer update --no-interaction

fix-right:
	sudo chmod -R 777 storage

clear-cache:
	docker exec -it what-if_php php artisan optimize