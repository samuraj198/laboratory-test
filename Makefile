.PHONY: help setup up stop artisan

.DEFAULT_GOAL := help

help:
	@echo Доступные команды
	@echo make help - вывод доступных команд
	@echo make setup - первый запуск проекта на новом устройстве
	@echo make up - запуск контейнеров
	@echo make stop - остановка контейнеров
	@echo make restart - перезапуск контейнеров
	@echo make artisan ... - ввод команд для контейнера app (make artisan make:model User)

setup:
	@if not exist src\.env ( \
		copy src\.env.example src\.env && \
		docker compose build --no-cache && \
		docker compose up -d && \
		docker-compose exec app php artisan key:generate && \
		docker-compose exec app php artisan migrate \
	) else ( \
		echo Проект уже настроен. Запускаю контейнеры... && \
		docker-compose up -d \
	)

up:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	docker-compose restart

artisan:
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

%:
	@: