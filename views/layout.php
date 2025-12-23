<?php
use App\Models\Notification;

$user = auth();
$successMessage = flash('success');
$errorMessage = flash('error');
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$filters = $filters ?? ['search' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token()) ?>">
    <title><?= htmlspecialchars($title ?? 'Online Library') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        },
                        // Role-specific colors
                        role: {
                            admin: {
                                bg: '#8B0000',      // Dark red
                                text: '#8B0000',
                                light: '#fee2e2'     // red-100
                            },
                            librarian: {
                                bg: '#50C878',       // Emerald green
                                text: '#059669',     // emerald-600
                                light: '#d1fae5'     // emerald-100
                            },
                            user: {
                                bg: '#3b82f6',       // Primary blue
                                text: '#2563eb',     // primary-600
                                light: '#dbeafe'     // primary-100
                            }
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-subtle': 'bounceSubtle 0.6s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        };
    </script>
    <style>
        .book-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .book-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @media (prefers-reduced-motion: reduce) {
            .book-card, * {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
</head>
<?php
$currentUser = auth();
$isAdmin = is_admin();
$isLibrarian = is_librarian();
$navBgClass = $isAdmin ? 'bg-red-50 dark:bg-red-900/20 border-b-2 border-red-200 dark:border-red-800' : ($isLibrarian ? 'bg-emerald-50 dark:bg-emerald-900/20 border-b-2 border-emerald-200 dark:border-emerald-800' : 'bg-white dark:bg-gray-800');
?>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300 flex flex-col">
    <nav class="<?= $navBgClass ?> shadow-sm sticky top-0 z-50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <?php if ($isAdmin): ?>
                        <a href="/" class="text-2xl font-bold bg-gradient-to-r from-red-700 to-red-900 bg-clip-text text-transparent hover:from-red-800 hover:to-red-950 transition-all duration-300">📚 <?= trans('app_name') ?></a>
                    <?php elseif ($isLibrarian): ?>
                        <a href="/" class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent hover:from-emerald-700 hover:to-emerald-900 transition-all duration-300">📚 <?= trans('app_name') ?></a>
                    <?php else: ?>
                        <a href="/" class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-purple-600 bg-clip-text text-transparent hover:from-primary-700 hover:to-purple-700 transition-all duration-300">📚 <?= trans('app_name') ?></a>
                    <?php endif; ?>
                    <div class="flex items-center gap-2">
                        <!-- Language Switcher -->
                        <div class="relative" data-dropdown>
                            <button id="languageToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" aria-label="<?= trans('select_language') ?>">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span id="currentLang" class="ml-1 text-xs font-medium text-gray-600 dark:text-gray-300"><?= strtoupper(get_language()) ?></span>
                            </button>
                            <div id="languageMenu" class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700">
                                <button data-lang="en" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <span class="text-lg">🇬🇧</span>
                                    <span>English</span>
                                </button>
                                <button data-lang="ru" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <span class="text-lg">🇷🇺</span>
                                    <span>Русский</span>
                                </button>
                                <button data-lang="kk" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <span class="text-lg">🇰🇿</span>
                                    <span>Қазақша</span>
                                </button>
                            </div>
                        </div>
                        <!-- Theme Toggle -->
                        <button id="themeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" aria-label="<?= trans('toggle_theme') ?>">
                            <svg id="sunIcon" class="w-5 h-5 text-gray-600 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg id="moonIcon" class="w-5 h-5 text-gray-300 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php 
                $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
                $isAuthPage = strpos($currentPath, '/login') !== false || strpos($currentPath, '/register') !== false;
                ?>
                <?php if (!$isAuthPage): ?>
                <div class="flex-1 max-w-xl mx-4">
                    <form action="/" method="GET" class="relative">
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                            placeholder="<?= trans('search_placeholder') ?>"
                            class="w-full px-4 py-2 pl-10 pr-4 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-gray-600 transition-colors"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                <div class="flex items-center space-x-4">
                    <?php if ($user): ?>
                        <?php
                        $notificationCount = 0;
                        try {
                            $notificationCount = Notification::countUnread((int) $user['user_id']);
                        } catch (\Throwable $e) {
                            // Table might not exist yet
                        }
                        ?>
                        <a href="/notifications" class="relative text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors" title="<?= trans('notifications') ?>" id="notificationsLink">
                            🔔
                            <span id="notificationBadge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full <?= $notificationCount > 0 ? '' : 'hidden' ?>">
                                <span id="notificationCount"><?= $notificationCount > 9 ? '9+' : $notificationCount ?></span>
                            </span>
                        </a>
                        <?php if (!is_librarian() && !is_admin()): ?>
                            <a href="/my-books" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= trans('my_books') ?></a>
                        <?php endif; ?>
                        <?php if (!is_librarian() && !is_admin()): ?>
                            <a href="/my-loans" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= trans('my_loans') ?></a>
                        <?php endif; ?>
                        <?php if (!is_librarian() && !is_admin()): ?>
                            <a href="/statistics" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= trans('statistics') ?></a>
                            <a href="/recommendations" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= trans('recommendations') ?></a>
                        <?php endif; ?>
                        <?php if (is_admin()): ?>
                            <a href="/admin/dashboard" class="bg-red-800 text-white hover:bg-red-900 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-md">
                                🔴 <?= trans('admin') ?>
                            </a>
                        <?php elseif (is_librarian()): ?>
                            <a href="/admin/dashboard" class="bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-md">
                                🟢 <?= trans('librarian') ?>
                            </a>
                        <?php endif; ?>
                        <div class="relative" data-dropdown>
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 focus:outline-none" data-dropdown-button>
                                <?php $avatar = media_url($user['avatar'] ?? null); ?>
                                <?php if ($avatar): ?>
                                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <?php 
                                    $userRole = strtolower($user['role'] ?? 'user');
                                    $avatarBg = get_role_color_classes($userRole, 'bg');
                                    ?>
                                    <div class="w-8 h-8 rounded-full <?= $avatarBg ?> flex items-center justify-center text-white font-semibold">
                                        <?= htmlspecialchars(strtoupper(substr($user['email'], 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="hidden md:block"><?= htmlspecialchars($user['first_name'] ?? $user['email']) ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700" data-dropdown-menu>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"><?= trans('profile') ?></a>
                                <form method="POST" action="/logout">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"><?= trans('logout') ?></button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors"><?= trans('login') ?></a>
                        <a href="/register" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 shadow-md hover:shadow-lg transition-all duration-300"><?= trans('register') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-4 flex-shrink-0">
        <div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

        <?php if ($successMessage): ?>
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative shadow-lg animate-slide-up" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative shadow-lg animate-slide-up" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative shadow-lg animate-slide-up" role="alert">
                <ul class="list-disc list-inside text-sm">
                    <?php foreach ($errors as $message): ?>
                        <li><?= htmlspecialchars($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        <?= $content ?>
    </main>

    <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 mt-12 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-600 dark:text-gray-400 text-sm">© <?= date('Y') ?> <?= trans('online_library') ?>. <?= trans('all_rights_reserved') ?>.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.min.js"></script>
    <script src="/js/app.js" defer></script>
    <script>
        // Language Switcher
        (function() {
            const languageToggle = document.getElementById('languageToggle');
            const languageMenu = document.getElementById('languageMenu');
            const currentLang = document.getElementById('currentLang');
            const langButtons = document.querySelectorAll('[data-lang]');
            
            // Get language from localStorage, cookie, or server default
            let currentLanguage = localStorage.getItem('language') || 
                                 getCookie('language') || 
                                 '<?= get_language() ?>';
            
            // Sync with server on page load
            if (currentLanguage !== '<?= get_language() ?>') {
                fetch('/api/language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ language: currentLanguage })
                }).catch(() => {
                    // Silent fail - language will be set on next change
                });
            }
            
            // Helper function to get cookie
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }
            
            // Update UI to show current language
            function updateLanguageUI() {
                const langMap = {
                    'ru': 'RU',
                    'kk': 'KK',
                    'en': 'EN'
                };
                if (currentLang) {
                    currentLang.textContent = langMap[currentLanguage] || 'EN';
                }
            }
            
            // Toggle language menu
            languageToggle?.addEventListener('click', function(e) {
                e.stopPropagation();
                languageMenu?.classList.toggle('hidden');
            });
            
            // Close menu on outside click
            document.addEventListener('click', function(e) {
                if (!languageToggle?.contains(e.target) && !languageMenu?.contains(e.target)) {
                    languageMenu?.classList.add('hidden');
                }
            });
            
            // Handle language selection
            langButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedLang = this.getAttribute('data-lang');
                    if (selectedLang && selectedLang !== currentLanguage) {
                        currentLanguage = selectedLang;
                        localStorage.setItem('language', selectedLang);
                        updateLanguageUI();
                        
                        // Send language change to server
                        fetch('/api/language', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ language: selectedLang })
                        }).then(() => {
                            // Reload page to apply translations
                            window.location.reload();
                        }).catch(() => {
                            // Still reload even if request fails
                            window.location.reload();
                        });
                    }
                    languageMenu?.classList.add('hidden');
                });
            });
            
            updateLanguageUI();
        })();
        
        // Notification count updater
        (function() {
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationCount = document.getElementById('notificationCount');
            
            function updateNotificationCount() {
                fetch('/api/notifications/count')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count || 0;
                        if (notificationCount) {
                            notificationCount.textContent = count > 9 ? '9+' : count;
                        }
                        if (notificationBadge) {
                            if (count > 0) {
                                notificationBadge.classList.remove('hidden');
                            } else {
                                notificationBadge.classList.add('hidden');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error updating notification count:', error);
                    });
            }
            
            // Update every 30 seconds
            setInterval(updateNotificationCount, 30000);
            
            // Update on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', updateNotificationCount);
            } else {
                updateNotificationCount();
            }
        })();
        
        // Theme Toggle
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');
            
            // Check for saved theme preference or default to light mode
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                html.classList.add('dark');
            }
            
            themeToggle?.addEventListener('click', function() {
                html.classList.toggle('dark');
                const isDark = html.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        })();
    </script>
</body>
</html>

