# 📚 Полное объяснение проекта "Online Library"

## 🏗️ Архитектура проекта

Проект построен на **чистом PHP** без использования фреймворков (Laravel, Symfony и т.д.). Используется паттерн **MVC (Model-View-Controller)** с собственной реализацией роутинга.

### Структура проекта:

```
libraryy/
├── config/              # Конфигурационные файлы
│   ├── database.php    # Настройки базы данных
│   └── session.php      # Настройки сессий
├── lang/               # Файлы переводов (мультиязычность)
│   ├── en.php          # Английский
│   ├── ru.php          # Русский
│   └── kk.php          # Казахский
├── public/             # Публичная директория (точка входа)
│   ├── index.php       # Главный роутер (все запросы идут сюда)
│   ├── js/
│   │   └── app.js      # JavaScript для фронтенда
│   ├── uploads/        # Загруженные файлы (книги, обложки)
│   └── seed.php        # Скрипт для заполнения БД тестовыми данными
├── src/                # Исходный код приложения
│   ├── bootstrap.php   # Инициализация приложения
│   ├── helpers.php     # Вспомогательные функции
│   ├── Router.php      # Класс роутера
│   ├── Controllers/    # Контроллеры (бизнес-логика)
│   ├── Models/         # Модели (работа с БД)
│   └── Support/       # Вспомогательные классы
│       └── Container.php  # Dependency Injection контейнер
├── sql/                # SQL скрипты для создания БД
├── storage/            # Хранилище (логи и т.д.)
└── views/              # Шаблоны (HTML/PHP)

```

---

## 🔄 Как работает приложение (Flow)

### 1. **Точка входа: `public/index.php`**

Когда пользователь заходит на сайт, запрос попадает в `public/index.php`:

```php
// 1. Проверка статических файлов (JS, CSS, изображения, PDF)
if (preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|pdf|epub|txt)$/i', $path)) {
    // Отдаем файл напрямую, без обработки
    readfile($filePath);
    exit;
}

// 2. Подключение bootstrap (инициализация)
require dirname(__DIR__) . '/src/bootstrap.php';

// 3. Создание роутера и регистрация маршрутов
$router = new Router();
$router->get('/', function() { ... });
$router->post('/login', function() { ... });
// ... и т.д.

// 4. Диспетчеризация запроса
$router->dispatch($method, $_SERVER['REQUEST_URI']);
```

### 2. **Инициализация: `src/bootstrap.php`**

Этот файл выполняется один раз при каждом запросе и настраивает окружение:

```php
// ✅ Загрузка конфигурации БД
$databaseConfig = require $rootPath . '/config/database.php';

// ✅ Запуск сессии PHP
session_start();

// ✅ Автозагрузка классов (PSR-4)
spl_autoload_register(function (string $class) {
    // Преобразует App\Controllers\BookController 
    // в src/Controllers/BookController.php
});

// ✅ Подключение вспомогательных функций
require_once $rootPath . '/src/helpers.php';

// ✅ Создание подключения к БД (singleton через Container)
Container::set('db', function() {
    return new PDO(...);
});

// ✅ Генерация CSRF токена
if (empty($_SESSION['_csrf_token'])) {
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

// ✅ Инициализация языка (из cookie или сессии)
$_SESSION['language'] = $_COOKIE['language'] ?? 'en';
```

### 3. **Роутинг: `src/Router.php`**

Собственный роутер с поддержкой параметров:

```php
// Регистрация маршрута
$router->get('/books/{book}', function(array $params) {
    // $params['book'] содержит ID книги из URL
    (new BookController())->show($params);
});

// Как это работает:
// 1. Паттерн "/books/{book}" преобразуется в regex: "#^/books/([^/]+)$#"
// 2. При запросе "/books/123" извлекается параметр: $params['book'] = '123'
// 3. Вызывается обработчик с этими параметрами
```

**Особенности:**
- Поддержка GET, POST, PUT, DELETE
- Параметры в фигурных скобках: `{book}`, `{user}`
- Если маршрут не найден → 404

### 4. **Контроллеры: `src/Controllers/`**

Контроллеры обрабатывают запросы и возвращают ответы:

```php
class BookController extends BaseController {
    public function show(array $params): void {
        // 1. Получение данных из БД через модель
        $book = Book::detail($bookId);
        
        // 2. Проверка авторизации и получение данных пользователя
        $user = auth();
        $isFavorite = Favorite::isFavorite($userId, $bookId);
        
        // 3. Рендеринг представления
        $this->view('books/show', [
            'book' => $book,
            'isFavorite' => $isFavorite,
            // ...
        ]);
    }
}
```

**Все контроллеры:**
- `HomeController` - главная страница (каталог книг)
- `BookController` - просмотр, чтение, скачивание книг
- `AuthController` - регистрация, вход, выход
- `AdminController` - админ-панель (управление книгами, пользователями)
- `LibraryController` - управление физическими книгами, займами, резервациями
- `FavoriteController` - избранное
- `RatingController` - рейтинги
- `CommentController` - комментарии
- `NotificationController` - уведомления
- `StatisticsController` - статистика чтения
- `RecommendationController` - рекомендации книг

### 5. **Модели: `src/Models/`**

Модели работают с базой данных:

```php
class Book {
    // Получить книгу по ID
    public static function find(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM books WHERE book_id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    // Поиск с фильтрами и пагинацией
    public static function paginate(array $filters, int $perPage, int $page): array {
        // Строит SQL запрос с WHERE, ORDER BY, LIMIT
        // Возвращает данные + информацию о пагинации
    }
}
```

**Все модели:**
- `Book` - книги
- `User` - пользователи
- `Favorite` - избранное
- `Rating` - рейтинги
- `Comment` - комментарии
- `Progress` - прогресс чтения
- `PhysicalBook` - физические экземпляры книг
- `BookLoan` - займы книг
- `BookReservation` - резервации
- `Notification` - уведомления
- `Statistics` - статистика
- `ActivityLog` - логи активности

### 6. **Представления (Views): `views/`**

Шаблоны используют PHP для рендеринга HTML:

```php
// views/books/show.php
<h1><?= htmlspecialchars($book['title']) ?></h1>
<p><?= htmlspecialchars($book['author']) ?></p>

<?php if ($isFavorite): ?>
    <button>Удалить из избранного</button>
<?php else: ?>
    <button>Добавить в избранное</button>
<?php endif; ?>
```

**Система шаблонов:**
- `layout.php` - основной макет (header, footer, навигация)
- `home.php` - главная страница
- `books/show.php` - страница книги
- `books/read.php` - читалка книг (PDF/EPUB)
- `auth/login.php`, `auth/register.php` - авторизация
- `admin/*` - админ-панель
- И т.д.

**Функция `view()`:**
```php
function view(string $template, array $data = []): void {
    // 1. Загружает шаблон (например, views/books/show.php)
    // 2. Извлекает переменные из $data в локальную область видимости
    // 3. Рендерит шаблон в строку
    // 4. Вставляет в layout.php
    // 5. Выводит результат
}
```

---

## 🔐 Система авторизации и ролей

### Роли пользователей:

1. **`user`** (обычный пользователь)
   - Просмотр книг
   - Чтение книг
   - Добавление в избранное
   - Оставление комментариев и рейтингов
   - Резервация физических книг

2. **`librarian`** (библиотекарь)
   - Все права пользователя
   - Управление физическими книгами
   - Выдача и возврат книг
   - Управление резервациями
   - Бан пользователей
   - Создание объявлений

3. **`admin`** (администратор)
   - Все права библиотекаря
   - Управление книгами (CRUD)
   - Управление пользователями
   - Просмотр логов активности
   - Управление комментариями

### Как работает авторизация:

```php
// Проверка авторизации
function require_auth(): void {
    if (!auth()) {
        redirect('/login');
    }
    
    // Обновление данных пользователя из БД (для проверки бана)
    $dbUser = User::find($userId);
    $_SESSION['user']['is_banned'] = $dbUser['is_banned'];
    
    // Если забанен - выход
    if ($_SESSION['user']['is_banned']) {
        session_destroy();
        redirect('/login');
    }
}

// Проверка роли
function require_admin(): void {
    require_auth();
    if (!is_admin()) {
        abort(403, 'Forbidden');
    }
}
```

**Сессия хранит:**
```php
$_SESSION['user'] = [
    'user_id' => 1,
    'email' => 'user@example.com',
    'role' => 'user',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'is_banned' => false
];
```

---

## 🌍 Мультиязычность

### Как работает:

1. **Язык хранится в сессии и cookie:**
```php
$_SESSION['language'] = 'ru'; // или 'en', 'kk'
setcookie('language', 'ru', time() + 365*24*60*60);
```

2. **Функция перевода:**
```php
trans('book_title') // Возвращает перевод из lang/{language}.php
```

3. **Файлы переводов:**
```php
// lang/ru.php
return [
    'book_title' => 'Название книги',
    'author' => 'Автор',
    // ...
];
```

4. **Переключение языка:**
```javascript
// JavaScript отправляет POST запрос на /api/language
fetch('/api/language', {
    method: 'POST',
    body: JSON.stringify({ language: 'ru' })
});
```

---

## 📖 Система чтения книг

### Поддерживаемые форматы:

1. **PDF** - через библиотеку PDF.js
2. **EPUB** - через ZipArchive (распаковка и рендеринг HTML)
3. **TXT** - простой текст

### Как работает PDF читалка:

```javascript
// public/js/app.js
function initPdfReader() {
    // 1. Загрузка PDF через PDF.js
    pdfjsLib.getDocument(pdfUrl).promise.then(pdfDoc => {
        // 2. Получение страницы
        pdfDoc.getPage(pageNum).then(page => {
            // 3. Создание viewport с масштабом
            const viewport = page.getViewport({ scale: zoomValue / 100 });
            
            // 4. Рендеринг на canvas
            page.render({ canvasContext: ctx, viewport });
        });
    });
}
```

**Особенности:**
- Двухстраничный режим (левая + правая страница)
- Масштабирование (zoom)
- Сохранение прогресса
- Полноэкранный режим
- Навигация: кнопки, клавиатура, свайпы

---

## 💾 База данных

### Основные таблицы:

1. **`users`** - пользователи
   - `user_id`, `email`, `password`, `role`, `first_name`, `last_name`

2. **`books`** - книги (цифровые)
   - `book_id`, `title`, `author`, `description`, `cover_image`, `book_file`, `genre`

3. **`book_translations`** - переводы книг (мультиязычность)
   - `book_id`, `language`, `title`, `author`, `description`, `genre`

4. **`physical_books`** - физические экземпляры
   - `physical_book_id`, `book_id`, `isbn`, `location`, `is_available`

5. **`book_loans`** - займы книг
   - `loan_id`, `user_id`, `physical_book_id`, `borrowed_at`, `due_date`, `returned_at`

6. **`book_reservations`** - резервации
   - `reservation_id`, `user_id`, `physical_book_id`, `status`, `expires_at`

7. **`favorites`** - избранное
8. **`ratings`** - рейтинги (1-5 звезд)
9. **`comments`** - комментарии
10. **`progress`** - прогресс чтения
11. **`notifications`** - уведомления
12. **`activity_logs`** - логи активности
13. **`user_bans`** - баны пользователей

### Связи (Foreign Keys):

- `favorites.user_id` → `users.user_id`
- `favorites.book_id` → `books.book_id`
- `book_loans.user_id` → `users.user_id`
- `book_loans.physical_book_id` → `physical_books.physical_book_id`
- И т.д.

---

## 🎨 Frontend (JavaScript)

### Основные функции в `public/js/app.js`:

1. **Toast уведомления:**
```javascript
showToast('Книга добавлена в избранное', 'success');
```

2. **AJAX запросы:**
```javascript
// Добавление в избранное
fetch('/books/123/favorite', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
});
```

3. **PDF Reader:**
```javascript
initBookReader() // Инициализация читалки
renderPdfPages() // Рендеринг страниц
goToNextPage()   // Переход на следующую страницу
applyZoom()      // Применение масштаба
```

4. **Автодополнение поиска:**
```javascript
// При вводе в поисковую строку отправляется запрос на /api/search/autocomplete
```

---

## 🔒 Безопасность

### Реализованные меры:

1. **CSRF защита:**
```php
// Генерация токена
$_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

// Проверка при POST запросах
verify_csrf($_POST['_token']);
```

2. **SQL Injection защита:**
```php
// Всегда используются prepared statements
$stmt = db()->prepare('SELECT * FROM books WHERE book_id = :id');
$stmt->execute(['id' => $bookId]);
```

3. **XSS защита:**
```php
// Всегда экранирование вывода
<?= htmlspecialchars($book['title']) ?>
```

4. **Проверка авторизации:**
```php
require_auth();      // Требует авторизации
require_admin();    // Требует роль admin
require_librarian(); // Требует роль librarian
```

5. **Проверка бана:**
```php
// При каждом запросе проверяется, не забанен ли пользователь
if ($_SESSION['user']['is_banned']) {
    session_destroy();
    redirect('/login');
}
```

---

## 📊 Система уведомлений

### Типы уведомлений:

1. **Напоминание о возврате книги** (за 3 дня до срока)
2. **Книга доступна** (резервация готова)
3. **Книга просрочена** (не возвращена вовремя)
4. **Новое объявление** (от библиотекаря)

### Как работает:

```php
// Создание уведомления
Notification::create($userId, 'book_available', [
    'book_id' => $bookId,
    'message' => 'Ваша резервированная книга готова'
]);

// Отображение в UI
$notifications = Notification::forUser($userId);
```

---

## 📈 Статистика и рекомендации

### Статистика пользователя:

- Количество прочитанных книг
- Прочитано страниц
- Средний рейтинг
- Топ жанры
- Достижения (achievements)
- График активности за год

### Рекомендации:

```php
// Основаны на:
// 1. Жанрах, которые пользователь читал
// 2. Рейтингах других пользователей
// 3. Похожих книгах
Statistics::getRecommendedBooks($userId, 4);
```

---

## 🚀 Как запустить проект

1. **Настройка БД:**
```php
// config/database.php
'database' => 'library_db',
'username' => 'root',
'password' => '',
```

2. **Создание БД:**
```bash
mysql -u root -p < sql/schema.sql
```

3. **Заполнение тестовыми данными (опционально):**
```bash
php public/seed.php
```

4. **Настройка веб-сервера:**
   - Apache/Nginx должен указывать на `public/` как document root
   - Или использовать PHP built-in server:
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Права на папки:**
```bash
chmod 775 public/uploads
```

---

## 🎯 Ключевые особенности проекта

1. **Чистый PHP** - без фреймворков, полный контроль
2. **MVC архитектура** - разделение логики
3. **Собственный роутер** - гибкая маршрутизация
4. **Мультиязычность** - поддержка 3 языков
5. **Роли и права** - admin, librarian, user
6. **Физические книги** - управление займами и резервациями
7. **PDF/EPUB читалка** - встроенная читалка с прогрессом
8. **Рекомендации** - умная система рекомендаций
9. **Уведомления** - система оповещений
10. **Статистика** - детальная аналитика чтения

---

## 📝 Заключение

Это полнофункциональная система управления онлайн-библиотекой с поддержкой:
- Цифровых книг (PDF, EPUB, TXT)
- Физических книг (займы, резервации)
- Пользовательских функций (избранное, рейтинги, комментарии)
- Административных функций (управление книгами, пользователями)
- Мультиязычности
- Статистики и рекомендаций

Проект написан на чистом PHP с использованием современных практик разработки.

