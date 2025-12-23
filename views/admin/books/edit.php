<?php
$book = $book ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$coverUrl = media_url($book['cover_image'] ?? null);
$isLibrarian = is_librarian();
?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-8"><?= trans('edit_book') ?></h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 border border-gray-100 dark:border-gray-700">
        <form method="POST" action="/admin/books/<?= (int) $book['book_id'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="_method" value="PUT">

            <div class="space-y-6">
                <?php 
                $translations = $translations ?? [];
                $transEn = $translations['en'] ?? [];
                ?>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('title') ?> (English) *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="<?= htmlspecialchars(old('title', $transEn['title'] ?? $book['title'] ?? '')) ?>"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['title'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['title']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('author') ?> (English) *</label>
                    <input 
                        type="text" 
                        id="author" 
                        name="author" 
                        value="<?= htmlspecialchars(old('author', $transEn['author'] ?? $book['author'] ?? '')) ?>"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['author'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['author']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="genre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('genre') ?> (English)</label>
                        <input 
                            type="text" 
                            id="genre" 
                            name="genre" 
                            value="<?= htmlspecialchars(old('genre', $transEn['genre'] ?? $book['genre'] ?? '')) ?>"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                        <?php if (isset($errors['genre'])): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['genre']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="isbn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('isbn') ?></label>
                        <input 
                            type="text" 
                            id="isbn" 
                            name="isbn" 
                            value="<?= htmlspecialchars(old('isbn', $book['isbn'] ?? '')) ?>"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="pages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('pages_count') ?></label>
                        <input 
                            type="number" 
                            id="pages" 
                            name="pages" 
                            value="<?= htmlspecialchars(old('pages', $book['pages'] ?? '')) ?>"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>

                    <div>
                        <label for="publication_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('publication_year') ?></label>
                        <input 
                            type="number" 
                            id="publication_year" 
                            name="publication_year" 
                            value="<?= htmlspecialchars(old('publication_year', $book['publication_year'] ?? '')) ?>"
                            min="1000"
                            max="<?= date('Y') + 1 ?>"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('language') ?></label>
                        <select 
                            id="language" 
                            name="language"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="en" <?= old('language', $book['language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="ru" <?= old('language', $book['language'] ?? '') === 'ru' ? 'selected' : '' ?>>Russian</option>
                            <option value="es" <?= old('language', $book['language'] ?? '') === 'es' ? 'selected' : '' ?>>Spanish</option>
                            <option value="fr" <?= old('language', $book['language'] ?? '') === 'fr' ? 'selected' : '' ?>>French</option>
                            <option value="de" <?= old('language', $book['language'] ?? '') === 'de' ? 'selected' : '' ?>>German</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="publisher" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('publisher') ?></label>
                    <input 
                        type="text" 
                        id="publisher" 
                        name="publisher" 
                        value="<?= htmlspecialchars(old('publisher', $book['publisher'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('description') ?> (English)</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    ><?= htmlspecialchars(old('description', $book['description'] ?? '')) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['description']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Translations Section -->
                <?php 
                $transRu = $translations['ru'] ?? [];
                $transKk = $translations['kk'] ?? [];
                ?>
                <div class="border-t dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4"><?= trans('translations') ?? 'Translations' ?></h3>
                    
                    <!-- Russian Translation -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">🇷🇺 Русский</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="title_ru" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('title') ?> (RU)</label>
                                <input type="text" id="title_ru" name="title_ru" value="<?= htmlspecialchars(old('title_ru', $transRu['title'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label for="author_ru" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('author') ?> (RU)</label>
                                <input type="text" id="author_ru" name="author_ru" value="<?= htmlspecialchars(old('author_ru', $transRu['author'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label for="description_ru" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('description') ?> (RU)</label>
                                <textarea id="description_ru" name="description_ru" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg"><?= htmlspecialchars(old('description_ru', $transRu['description'] ?? '')) ?></textarea>
                            </div>
                            <div>
                                <label for="genre_ru" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('genre') ?> (RU)</label>
                                <input type="text" id="genre_ru" name="genre_ru" value="<?= htmlspecialchars(old('genre_ru', $transRu['genre'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kazakh Translation -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">🇰🇿 Қазақша</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="title_kk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('title') ?> (KK)</label>
                                <input type="text" id="title_kk" name="title_kk" value="<?= htmlspecialchars(old('title_kk', $transKk['title'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label for="author_kk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('author') ?> (KK)</label>
                                <input type="text" id="author_kk" name="author_kk" value="<?= htmlspecialchars(old('author_kk', $transKk['author'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label for="description_kk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('description') ?> (KK)</label>
                                <textarea id="description_kk" name="description_kk" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg"><?= htmlspecialchars(old('description_kk', $transKk['description'] ?? '')) ?></textarea>
                            </div>
                            <div>
                                <label for="genre_kk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('genre') ?> (KK)</label>
                                <input type="text" id="genre_kk" name="genre_kk" value="<?= htmlspecialchars(old('genre_kk', $transKk['genre'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('cover_image') ?></label>
                    <?php if ($coverUrl): ?>
                        <div class="mb-2">
                            <img src="<?= htmlspecialchars($coverUrl) ?>" alt="Current cover" class="w-32 h-40 object-cover rounded">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Current cover image</p>
                        </div>
                    <?php endif; ?>
                    <input 
                        type="file" 
                        id="cover_image" 
                        name="cover_image" 
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-300 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50"
                        onchange="showFileName(this, 'cover_image_name')"
                    >
                    <p id="cover_image_name" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
                    <?php if (isset($errors['cover_image'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['cover_image']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="book_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('book_file') ?></label>
                    <?php if (!empty($book['book_file'])): ?>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current file: <?= htmlspecialchars(basename($book['book_file'])) ?></p>
                    <?php endif; ?>
                    <input 
                        type="file" 
                        id="book_file" 
                        name="book_file" 
                        accept=".pdf,.epub,.txt"
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-300 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50"
                        onchange="showFileName(this, 'book_file_name')"
                    >
                    <p id="book_file_name" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
                    <?php if (isset($errors['book_file'])): ?>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['book_file']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <button 
                    type="submit" 
                    class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 font-medium"
                >
                    <?= trans('update_book') ?>
                </button>
                <a href="/admin/books" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium">
                    <?= trans('cancel') ?>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function showFileName(input, targetId) {
    const target = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
        target.textContent = 'Selected: ' + fileName + ' (' + fileSize + ' MB)';
        target.classList.remove('hidden');
    } else {
        target.textContent = '';
        target.classList.add('hidden');
    }
}
</script>

