<?php
$physicalBook = $physicalBook ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Edit Physical Book</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Update physical book information</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($physicalBook['title']) ?></h3>
        <p class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($physicalBook['author']) ?></p>
    </div>

    <form method="POST" action="/admin/library/physical-books/<?= (int) $physicalBook['physical_book_id'] ?>" class="space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="_method" value="PUT">

        <div>
            <label for="inventory_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inventory Number *</label>
            <input type="text" name="inventory_number" id="inventory_number" required value="<?= htmlspecialchars(old('inventory_number', $physicalBook['inventory_number'] ?? '')) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <?php if (isset($errors['inventory_number'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['inventory_number']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location (Shelf)</label>
            <input type="text" name="location" id="location" value="<?= htmlspecialchars(old('location', $physicalBook['location'] ?? '')) ?>" placeholder="e.g., A-12-3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_available" value="1" <?= !empty($physicalBook['is_available']) ? 'checked' : '' ?> class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Available for loan</span>
            </label>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars(old('notes', $physicalBook['notes'] ?? '')) ?></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all">Update Physical Book</button>
            <a href="/admin/library/physical-books" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</div>

