<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AnnouncementController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\CommentController;
use App\Controllers\FavoriteController;
use App\Controllers\HomeController;
use App\Controllers\LibraryController;
use App\Controllers\MyBooksController;
use App\Controllers\NotificationController;
use App\Controllers\ProfileController;
use App\Controllers\RatingController;
use App\Controllers\RecommendationController;
use App\Controllers\ReservationController;
use App\Controllers\StatisticsController;
use App\Router;

// Serve static files directly
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Check if it's a static file request
if (preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|pdf|epub|txt)$/i', $path)) {
    $filePath = __DIR__ . $path;
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'epub' => 'application/epub+zip',
            'txt' => 'text/plain',
        ];
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        readfile($filePath);
        exit;
    }
}

require dirname(__DIR__) . '/src/bootstrap.php';

$router = new Router();

$router->get('/', function () {
    (new HomeController())->index();
});

$router->get('/login', function () {
    if (auth()) {
        redirect('/');
    }
    (new AuthController())->showLogin();
});

$router->post('/login', function () {
    (new AuthController())->login();
});

$router->get('/register', function () {
    if (auth()) {
        redirect('/');
    }
    (new AuthController())->showRegister();
});

$router->post('/register', function () {
    (new AuthController())->register();
});

$router->post('/logout', function () {
    require_auth();
    (new AuthController())->logout();
});

$router->get('/my-books', function () {
    (new MyBooksController())->index();
});

$router->get('/books/{book}', function (array $params) {
    (new BookController())->show($params);
});

$router->get('/books/{book}/read', function (array $params) {
    (new BookController())->read($params);
});

$router->get('/books/{book}/download', function (array $params) {
    (new BookController())->download($params);
});

$router->post('/books/{book}/progress', function (array $params) {
    (new BookController())->updateProgress($params);
});

$router->post('/books/{book}/favorite', function (array $params) {
    (new FavoriteController())->toggle($params);
});

$router->post('/books/{book}/rating', function (array $params) {
    (new RatingController())->store($params);
});

$router->post('/books/{book}/reserve', function (array $params) {
    (new ReservationController())->store($params);
});

$router->post('/books/{book}/comments', function (array $params) {
    (new CommentController())->store($params);
});

$router->post('/comments/{comment}/delete', function (array $params) {
    (new CommentController())->destroy($params);
});

$router->post('/comments/{comment}/like', function (array $params) {
    (new CommentController())->toggleLike($params);
});

$router->delete('/reservations/{reservation}', function (array $params) {
    (new ReservationController())->cancel($params);
});

// Profile routes
$router->get('/profile', function () {
    (new ProfileController())->show();
});

$router->put('/profile', function () {
    (new ProfileController())->update();
});

$router->put('/profile/password', function () {
    (new ProfileController())->updatePassword();
});

// Admin routes
$router->get('/admin/dashboard', function () {
    (new AdminController())->dashboard();
});

$router->get('/admin/books', function () {
    (new AdminController())->booksIndex();
});

$router->get('/admin/books/create', function () {
    (new AdminController())->booksCreate();
});

$router->post('/admin/books', function () {
    (new AdminController())->booksStore();
});

$router->get('/admin/books/{book}/edit', function (array $params) {
    (new AdminController())->booksEdit($params);
});

$router->put('/admin/books/{book}', function (array $params) {
    (new AdminController())->booksUpdate($params);
});

$router->delete('/admin/books/{book}/delete', function (array $params) {
    (new AdminController())->booksDestroy($params);
});

$router->get('/admin/users', function () {
    (new AdminController())->usersIndex();
});

$router->put('/admin/users/{user}/role', function (array $params) {
    (new AdminController())->usersUpdateRole($params);
});

$router->delete('/admin/users/{user}/delete', function (array $params) {
    (new AdminController())->usersDestroy($params);
});

$router->get('/admin/comments', function () {
    (new AdminController())->commentsIndex();
});

$router->get('/admin/logs', function () {
    (new AdminController())->logs();
});

$router->delete('/admin/comments/{comment}/delete', function (array $params) {
    (new AdminController())->commentsDestroy($params);
});

// Announcements (for librarians)
$router->get('/admin/announcements/create', function () {
    (new AnnouncementController())->create();
});

$router->post('/admin/announcements', function () {
    (new AnnouncementController())->store();
});

// Library management routes (for librarians)
$router->get('/admin/library/physical-books', function () {
    (new LibraryController())->physicalBooksIndex();
});

$router->get('/admin/library/physical-books/create', function () {
    (new LibraryController())->physicalBooksCreate();
});

$router->post('/admin/library/physical-books', function () {
    (new LibraryController())->physicalBooksStore();
});

$router->get('/admin/library/physical-books/{id}/edit', function (array $params) {
    (new LibraryController())->physicalBooksEdit($params);
});

$router->put('/admin/library/physical-books/{id}', function (array $params) {
    (new LibraryController())->physicalBooksUpdate($params);
});

$router->delete('/admin/library/physical-books/{id}/delete', function (array $params) {
    (new LibraryController())->physicalBooksDestroy($params);
});

$router->get('/admin/library/loans', function () {
    (new LibraryController())->loansIndex();
});

$router->get('/admin/library/loans/create', function () {
    (new LibraryController())->loansCreate();
});

$router->post('/admin/library/loans', function () {
    (new LibraryController())->loansStore();
});

$router->post('/admin/library/loans/{id}/return', function (array $params) {
    (new LibraryController())->loansReturn($params);
});

$router->get('/admin/library/bans', function () {
    (new LibraryController())->bansIndex();
});

$router->get('/admin/library/bans/create', function () {
    (new LibraryController())->bansCreate();
});

$router->get('/admin/library/reservations', function () {
    (new LibraryController())->reservationsIndex();
});

$router->post('/admin/library/reservations/{id}/mark-ready', function (array $params) {
    (new LibraryController())->reservationMarkReady($params);
});

$router->post('/admin/library/bans', function () {
    (new LibraryController())->bansStore();
});

$router->delete('/admin/library/bans/{user}/unban', function (array $params) {
    (new LibraryController())->bansDestroy($params);
});

// Student routes
$router->get('/my-loans', function () {
    (new LibraryController())->myLoans();
});

// Notifications
$router->get('/notifications', function () {
    (new NotificationController())->index();
});

$router->post('/notifications/{id}/mark-read', function (array $params) {
    (new NotificationController())->markAsRead($params);
});

$router->post('/notifications/mark-all-read', function () {
    (new NotificationController())->markAllAsRead();
});

$router->get('/api/notifications/count', function () {
    (new NotificationController())->count();
});

// Statistics and recommendations
$router->get('/statistics', function () {
    (new StatisticsController())->userStats();
});

$router->get('/recommendations', function () {
    (new RecommendationController())->index();
});

$router->get('/export/favorites', function () {
    (new StatisticsController())->exportFavorites();
});

// Search autocomplete API
$router->get('/api/search/autocomplete', function () {
    header('Content-Type: application/json');
    
    $query = trim($_GET['q'] ?? '');
    if (strlen($query) < 2) {
        echo json_encode([]);
        exit;
    }

    $stmt = db()->prepare('
        SELECT book_id, title, author
        FROM books
        WHERE title LIKE :query OR author LIKE :query
        ORDER BY title ASC
        LIMIT 10
    ');
    $stmt->execute(['query' => '%' . $query . '%']);
    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    echo json_encode($results);
    exit;
});

// Language change API
$router->post('/api/language', function () {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $language = $input['language'] ?? '';
    
    // Validate language
    $allowedLanguages = ['en', 'ru', 'kk'];
    if (!in_array($language, $allowedLanguages, true)) {
        json(['success' => false, 'message' => 'Invalid language'], 400);
        return;
    }
    
    // Set language in session
    set_language($language);
    
    // Also set cookie for persistence
    setcookie('language', $language, time() + (365 * 24 * 60 * 60), '/'); // 1 year
    
    json(['success' => true, 'language' => $language]);
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$router->dispatch($method, $_SERVER['REQUEST_URI'] ?? '/');
