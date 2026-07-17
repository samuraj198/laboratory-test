# Это тестовое задание для компании "Лаборатория Интернет"

## Как запустить проект
1. Если у вас установлена утилита make на пк
   - make setup
   - Вставить API ключи в .env файл (ключи пришлю в личку)
   - make artisan queue:work
2. Если нет
   - copy src\.env.example src\.env
   - docker compose build --no-cache
   - docker compose up -d
   - docker-compose exec app php artisan key:generate
   - docker-compose exec app php artisan migrate
   - Вставить API ключи в .env файл (ключи пришлю в личку)
   - make artisan queue:work

## Стек технологий
- Backend: PHP/Laravel. Из библиотек - Ziggy, Inertia, resend-laravel, swagger, ai, gigachat-php
- AI: библиотека от Laravel laravel/ai и GigaChat от сбербанка

## Архитектура
- Стандартная структура fullstack связки Laravel + Inertia.js + Vue3
- Паттерны - Service-pattern, DI-pattern, Event-pattern, Form Request-pattern, Facade-pattern
- Объяснение выбора технологий
  - Сервисы использовал для того чтобы отделить бизнес логику от контроллеров
  - DI использовал для быстрого подключения файлов
  - Event для того чтобы не засорять сервис jobs'ами
  - Form Request использовал для того чтобы вынести валидацию данных из контроллера
  - Facades использовал для удобства кодирования

## Реализация API
- Реализован только 1 endpoint для сохранения вопроса (комментария) пользователя в базу
  - /api/contact
- Пример запроса
  - curl -X 'POST' \
    'http://localhost:8000/api/contact' \
    -H 'accept: application/json' \
    -H 'Content-Type: application/json' \
    -H 'X-CSRF-TOKEN: ' \
    -d '{
    "name": "Some name",
    "phone": "8900000000",
    "email": "example@example.ru",
    "comment": "Some comment"
    }'
- Пример ответа
  - { <br>
    "success": true,<br>
    "message": "Запрос успешно сохранен в бд",<br>
    "data": {<br>
        "name": "Some Name",<br>
        "email": "example@example.ru",<br>
        "phone": "8900000000",<br>
        "comment": "Some comment",<br>
        "updated_at": "2026-07-17T18:07:41.000000Z",<br>
        "created_at": "2026-07-17T18:07:41.000000Z",<br>
        "id": 24<br>
        }<br>
    }<br>
- Валидация данных написана в отдельном Form Request, а обработка ошибок написана в основном файле (bootstrap/app.php)

## AI-интеграция
- Выбрал GigaChat AI, потому что его легко подключить и он бесплатный. Он в режиме очереди отвечает на легкие вопросы пользователей (из-за того, что нейронка бесплатная и нет особо контекста в задании, нейронка отвечает не всегда так, как хочется)
- fallback организован через try catch, если что-то идет не так, у очереди есть еще 3 попытки на верный исход, если не получается, то ответ просто не записывается (Если нейронка посчитает, что запрос тяжелый, то ответа тоже не будет)
- Промпт лежит в листенере AITryAnswerToContact

## Что сделано с помощью AI
- Генерировались те части кода, в которых находится код, связынный с GigaChat и Resend (отправка emails)
- Использовал стандартные запросы нейронке с тем, что мне нужно и тем, что уже есть
- Не всегда нейронка понимает все что нужно, а иногда я не рассказывал весь контекст

## Хранение данных
- Логи сохраняются по пути storage/logs/laravel.log
- Rate limiting организованы через middleware throttle за маршрут (ограничение 30 запросов в минуту)
