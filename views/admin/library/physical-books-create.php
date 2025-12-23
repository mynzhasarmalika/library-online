<?php
$book = $book ?? null;
$books = $books ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Add Physical Book</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Add a physical copy to the library</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <form method="POST" action="/admin/library/physical-books" class="space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div>
            <label for="book_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Book *</label>
            <select name="book_id" id="book_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <option value="">Select a book...</option>
                <?php foreach ($books as $b): ?>
                    <option value="<?= (int) $b['book_id'] ?>" <?= $book && (int) $book['book_id'] === (int) $b['book_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['title']) ?> - <?= htmlspecialchars($b['author']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['book_id'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['book_id']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="inventory_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inventory Number *</label>
            <input type="text" name="inventory_number" id="inventory_number" required value="<?= htmlspecialchars(old('inventory_number', '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <?php if (isset($errors['inventory_number'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['inventory_number']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location (Shelf)</label>
            <input type="text" name="location" id="location" value="<?= htmlspecialchars(old('location', '')) ?>" placeholder="e.g., A-12-3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars(old('notes', '')) ?></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all">Add Physical Book</button>
            <a href="/admin/library/physical-books" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</div>

