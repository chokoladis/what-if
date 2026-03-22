## 📀 Установка
### Если хотите установить с нуля
1) Подготавливаете файл .env(.env.example), 
2) Скачивание и запуск контейнеров - `Make up`
3) Установка композера - `make install-composer`
4) Запуск миграций и фейк данных - `Make db-start`
5) в контейнере запуск комманды - `php artisan reverb:start` (Временное решение)
### Другой вариант установки
1-3) аналогично
4) `Make db-restore` (Есть вероятность не актуальной структуры)
## Техническая часть
### 🧪 Используемые технологии/инструменты
> - Laravel + Vite, JS, JQuery, Html, Scss
> - Filament
> - Meilisearch
> - WebSocket (reverb)
> - Queue (БД)
> - Docker, Docker-compose (nginx, php-fpm, node, mysql, redis)
> - Дебаг пакет - barryvdh/laravel-debugbar
> - Встроен gemini.ai (проверка картинок на запрещенку)
### 🔬 [Тесты](https://github.com/chokoladis/what-if/tree/main/readme/tests)


## 📸 Наглядное представление функционала
<br>
<p align="center">
    <img src="/readme/main_page.png" style="width: 100%; max-width: 800px;" />
</p>
<b>Главная страница</b>

### 🔥 [Основной функционал](https://github.com/chokoladis/what-if/tree/main/readme/main)
### 👤 [Профиль](https://github.com/chokoladis/what-if/tree/main/readme/profile)
### ⚙ Admin

- CRUD категории, вопросов, тегов, комментариев
- Настройки: Капчи, использования gemini, умного кэширования*1
- Очистка кэша

Ещё в разработке
- Просмотр и ответ на оставленные заявки?
- CRUD Юзеров
- Кол-во новых сущностей в виде бейджов

<br><br>
<p align="center">
    <img src="/readme/dashboard_settings.png" alt="admin dashboard" style="width: 100%; max-width: 800px;" />
</p>
<b>Настройки в админ панели</b>
<br><br>
<p align="center">
    <img src="/readme/admin_dasboard.png" alt="admin dashboard" style="width: 100%; max-width: 800px;" />
</p>
<b>Админка v2</b>

<br><br>
`
*1 - Умное кеширование - сброс кеша по сущности или смежной сущности, при CRUD операции (если поддерживается)
`