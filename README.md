# library-online
# Online Library Website

A modern, full-featured online library management system built with plain PHP and Tailwind CSS.

## Features

### User Features
- **Authentication**: Secure registration and login system
- **Book Catalog**: Browse books with search and filtering (by genre, author)
- **Reading**: Read books with progress tracking
- **Favorites**: Save favorite books
- **Ratings**: Rate books (1-5 stars)
- **Comments**: Leave and view comments on books
- **Personal Library**: Track favorites, currently reading, and completed books
- **Profile Management**: Update profile, avatar, and password

### Admin Features
- **Book Management**: Full CRUD operations for books
- **User Management**: View users, change roles, delete accounts
- **Comment Moderation**: Review and delete inappropriate comments
- **Dashboard**: Overview of system statistics

## Technology Stack

- **Backend**: Plain PHP 8.1+
- **Database**: MySQL/PostgreSQL (via PDO)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS)
- **Styling**: Tailwind CSS (via CDN)
- **Sessions**: Native PHP sessions
- **Security**: CSRF protection, password hashing

## Installation

### Prerequisites
- PHP 8.1 or higher
- MySQL or PostgreSQL
- Web server (Apache/Nginx) or PHP built-in server

### Setup Steps

1. **Clone or download the repository**
   ```bash
   cd libraryy
   ```

2. **Configure database**
   
   Edit `config/database.php` with your database credentials:
   ```php
   return [
       'driver' => 'mysql', // or 'pgsql'
       'host' => '127.0.0.1',
       'port' => '3306',
       'database' => 'library_db',
       'username' => 'root',
       'password' => 'your_password',
       'charset' => 'utf8mb4',
       'options' => [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       ],
   ];
   ```

3. **Create database and import schema**
   
   Create a database and import the schema:
   ```bash
   mysql -u root -p library_db < sql/schema.sql
   ```
   
   Or for PostgreSQL:
   ```bash
   psql -U postgres -d library_db -f sql/schema.sql
   ```

4. **Fill database with sample data (optional)**
   
   **Option A: Using SQL file**
   ```bash
   mysql -u root -p library_db < sql/seed_data.sql
   ```
   
   **Option B: Using PHP seeder script**
   ```bash
   php public/seed.php
   ```
   
   Or visit in browser: http://localhost:8000/seed.php
   
   This will create:
   - Admin user: `admin@library.com` / `password`
   - Test users: `john@example.com`, `jane@example.com`, `bob@example.com` / `password`
   - 20 sample books with various genres
   - Sample ratings, favorites, comments, and reading progress

5. **Set up web server**

   **Option A: PHP Built-in Server (Development)**
   ```bash
   php -S localhost:8000 -t public
   ```
   Then visit: http://localhost:8000

   **Option B: Apache/Nginx (Production)**
   
   Configure your web server to point to the `public/` directory as document root.
   
   For Apache, add to `.htaccess` in `public/`:
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]
   ```

6. **Set permissions**
   
   Make sure `public/uploads/` directory is writable:
   ```bash
   chmod -R 775 public/uploads
   ```

## User Roles

The system has three roles:

1. **Admin** - Full system access, can manage everything including roles
2. **Librarian** - Can manage books and users, but cannot:
   - Change roles to admin/librarian
   - Delete admin or librarian accounts
3. **User** - Regular users who can read, comment, and rate books

## Creating Users

### Creating an Admin User

```sql
INSERT INTO users (email, password, password_hash, role, first_name, last_name, created_at, updated_at)
VALUES (
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password'
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'Admin',
    'User',
    NOW(),
    NOW()
);
```

### Creating a Librarian User

```sql
INSERT INTO users (email, password, password_hash, role, first_name, last_name, created_at, updated_at)
VALUES (
    'librarian@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password'
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'librarian',
    'Library',
    'Staff',
    NOW(),
    NOW()
);
```

### Hash Password in PHP

```php
<?php
// hash_password.php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

**Note:** Before creating users, make sure to run the migration to add the librarian role:
```sql
ALTER TABLE users MODIFY COLUMN role ENUM('admin','librarian','user') NOT NULL DEFAULT 'user';
```

## Project Structure

```
libraryy/
├── config/
│   ├── database.php         # Database configuration
│   └── session.php          # Session configuration (optional)
├── public/
│   ├── index.php            # Entry point and router
│   └── uploads/            # User uploads (avatars, covers, books)
├── src/
│   ├── bootstrap.php       # Autoloader, DB connection, session setup
│   ├── Router.php          # Custom router
│   ├── Controllers/        # All controllers
│   ├── Models/             # Data access layer
│   ├── Support/            # Helper classes (Container)
│   └── helpers.php         # Global helper functions
├── views/
│   ├── layout.php          # Main layout template
│   ├── home.php            # Home page
│   ├── auth/               # Login/Register pages
│   ├── books/               # Book detail and reading pages
│   ├── my-books/           # Personal library
│   ├── profile/            # User profile
│   └── admin/              # Admin panel pages
├── storage/
│   └── logs/               # Application logs
└── sql/
    └── schema.sql          # Database schema
```

## Design Features

- **Modern UI**: Clean, minimalist design with generous whitespace
- **Responsive**: Works seamlessly on desktop, tablet, and mobile
- **Interactive Elements**: Hover effects, smooth transitions
- **Color Scheme**: Primary blue accent color with neutral backgrounds
- **Typography**: Modern sans-serif fonts for readability
- **Tailwind CSS**: Styled via CDN, no build step required

## Security

- CSRF protection on all forms
- Password hashing with bcrypt (`password_hash`)
- Role-based access control (RBAC)
- Secure file uploads with validation
- SQL injection protection via PDO prepared statements
- XSS protection via `htmlspecialchars()` in all views

## Development

### Adding New Routes

Edit `public/index.php`:

```php
$router->get('/your-route', function () {
    (new YourController())->method();
});
```

### Adding New Controllers

Create a file in `src/Controllers/`:

```php
<?php
declare(strict_types=1);

namespace App\Controllers;

final class YourController extends BaseController
{
    public function method(): void
    {
        $this->view('your/view', [
            'title' => 'Page Title',
            // ... data
        ]);
    }
}
```

### Database Queries

Use the `db()` helper function:

```php
$stmt = db()->prepare('SELECT * FROM table WHERE id = :id');
$stmt->execute(['id' => $id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

