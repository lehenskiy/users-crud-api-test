# users-crud-api
## Requirement Specification
Разработайте простое API для веб-приложения, которое позволит клиентским приложениям взаимодействовать с базой данных пользователей. 
1) API должно предоставлять эндпоинты для создания, чтения, обновления и удаления пользователей. 
2) API должно быть защищено с использованием механизма аутентификации, такого как токены доступа.

## Deployment
1) `docker-compose up -d --build`
2) `docker exec -it uca-php bash`
3) `composer install`
4) `bin/console doctrine:migrations:migrate`

## Methods and URLs
Access via - http://localhost:1338/api

### Access tokens:
`POST` /signup

`POST` /auth

### Operations on users:
*Create*: `POST` /users

*Read*: `GET` /users or /user/id

*Update*: `PATCH` /user/id

*Delete*: `DELETE` /user/id

#### Note
Не был использован маппинг данных запроса в аргумент контроллера(https://symfony.com/blog/new-in-symfony-6-3-mapping-request-data-to-typed-objects)
