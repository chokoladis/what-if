## 📀 Quick Start

```bash
cp .env.example .env
make up
make install-composer
make db-fake-run
```

**Note (for notifications)**: Reverb startup is currently manual; worker daemonization is in the roadmap.

```bash
  docker exec -it what-if_php php artisan reverb:start
```
**Run test cases**
```bash
  make tests-run
```

## 🛠 Tech
### 🧪 Stack & Architecture
* Backend: Laravel 11, PHP 8.3
* Admin Panel: Filament
* Realtime: Laravel Reverb (WebSockets)
* Search Engine: Meilisearch integration
* Infrastructure: Docker-compose (Nginx, PHP-FPM, MySQL, Redis)
### 🛡 Quality & Tooling
* Static Analysis: PHPStan (Level 8)
* Testing: PHPUnit (function), JMeter/Postman for load testing.
* AI Integration: Gemini AI for content moderation.

### 🔬 [Tests](https://github.com/chokoladis/what-if/tree/main/readme/tests)

## 📸 Visual representation of the functionality
<br>
<p align="center">
    <img src="/readme/main_page.png" style="width: 100%; max-width: 800px;" />
</p>
<b>Mainpage</b>

### 🔥 [Main functional](https://github.com/chokoladis/what-if/tree/main/readme/main)
### 👤 [Profile](https://github.com/chokoladis/what-if/tree/main/readme/profile)
### ⚙ Admin

* Smart Caching: Event-driven cache invalidation for related entities.
* Full CRUD for categories, tags, and comments with integrated captcha.
* Captcha, smart cache settings

Still in development
- View and respond to submitted requests?
- User CRUD
- Number of new entities in the form of badges

<br><br>
<p align="center">
    <img src="/readme/dashboard_settings.png" alt="admin dashboard" style="width: 100%; max-width: 800px;" />
</p>
<b>Settings in admin panel</b>
<br><br>
<p align="center">
    <img src="/readme/admin_dasboard.png" alt="admin dashboard" style="width: 100%; max-width: 800px;" />
</p>
<b>Admin panel v2</b>

<br><br>