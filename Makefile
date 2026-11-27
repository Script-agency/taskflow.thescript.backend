.PHONY: up down build migrate test bash logs ps composer-install up-dev recreate

# Переменные
DOCKER_COMPOSE 	= docker compose
EXEC_APP 		= docker exec -it app-backend
EXEC_PHP 		= $(EXEC_APP) php
EXEC_COMPOSER 	= $(EXEC_APP) composer

# Запуск всех контейнеров (production)
up:
	$(DOCKER_COMPOSE) up -d

# Запуск в dev-режиме с Xdebug и dev-зависимостями
up-dev:
	BUILD_MODE=dev $(DOCKER_COMPOSE) up -d --build

# Остановка и удаление контейнеров
down:
	$(DOCKER_COMPOSE) down

# Сборка контейнера (production)
build:
	$(DOCKER_COMPOSE) up -d --build

# Пересоздать один контейнер по имени (docker compose up -d --force-recreate)
recreate:
	@if [ -z "$(filter-out $@,$(MAKECMDGOALS))" ]; then \
		echo "Ошибка: укажите имя контейнера. Пример: make recreate app-backend"; \
		exit 1; \
	fi
	$(DOCKER_COMPOSE) up -d --force-recreate $(filter-out $@,$(MAKECMDGOALS))

# Применение миграций БД
migrate:
	$(EXEC_PHP) yii migrate --interactive=0

# Запуск всех тестов
test:
	$(EXEC_PHP) vendor/bin/codecept run

# Вход в bash-оболочку контейнера
bash:
	$(EXEC_APP) bash

# Просмотр логов всех контейнеров
logs:
	$(DOCKER_COMPOSE) logs -f

# Список запущенных контейнеров
ps:
	$(DOCKER_COMPOSE) ps

# Установка всех PHP-зависимостей (включая dev)
composer-install:
	$(EXEC_COMPOSER) install

%:
	@: