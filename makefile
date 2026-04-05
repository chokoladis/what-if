include .env

build:
	docker-compose up --build -d
	make fix-right
up:
	docker-compose up -d
	make fix-right
down:
	docker-compose stop

reload:
	make down
	make up

#db
db-fake-run:
	docker exec -w /var/www/what_if what-if_php php artisan migrate
	docker exec -w /var/www/what_if what-if_php sh -c "php artisan db:seed DatabaseSeeder && php artisan db:seed QuestionSeeder"
	docker exec -w /var/www/what_if what-if_php sh -c "php artisan db:seed TagSeeder && php artisan db:seed QuestionTagsSeeder && php artisan db:seed UserTagsSeeder"

db-restore:
	gunzip -c dumps/what_if.sql.gz | docker exec -i what-if_mysql mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE);
db-export:
	docker exec what-if_mysql mysqldump -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) | gzip > dumps/what_if_$(shell date +%F).sql.gz

#composer
install-composer:
	docker exec -w /var/www/what_if what-if_php composer install --no-interaction --prefer-dist --optimize-autoloader
update-composer:
	docker exec -w /var/www/what_if what-if_php composer update --no-interaction

#other
fix-right:
	docker-compose exec php chown -R www-data:www-data /var/www/what_if/storage/app
	docker-compose exec php chown -R www-data:www-data /var/www/what_if/storage/logs
	docker-compose exec php chown -R www-data:www-data /var/www/what_if/bootstrap/cache
	docker-compose exec php chmod -R 755 /var/www/what_if/storage/logs
	docker-compose exec php chmod -R 755 /var/www/what_if/bootstrap/cache

clear-cache:
	docker exec -it what-if_php php artisan optimize:clear

tests-run:
	docker exec -w /var/www/what_if what-if_php php artisan test