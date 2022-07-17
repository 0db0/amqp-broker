UID := $(shell id -u)

up:
	cp .env.example .env
	docker-compose build --build-arg uid=$(UID)
	docker-compose up -d
	./scripts/composer.sh install

stop:
	docker-compose stop

down:
	docker-compose down --remove-orphans

ps:
	docker-compose ps

composer:
	./scripts/composer.sh $(c)