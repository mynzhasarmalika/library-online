<?php
$user = $user ?? null;
$physicalBook = $physicalBook ?? null;
$reservation = $reservation ?? null;
$users = $users ?? [];
$physicalBooks = $physicalBooks ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100"><?= trans('loan_book') ?></h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">
        <?= $reservation ? trans('issue_book_from_reservation') : trans('track_loans_returns') ?>
    </p>
    <?php if ($reservation): ?>
        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-800 dark:text-blue-300">
                <strong><?= trans('reservation_id') ?>:</strong> #<?= (int) $reservation['reservation_id'] ?> | 
                <strong><?= trans('book') ?>:</strong> <?= htmlspecialchars($reservation['title']) ?> | 
                <strong><?= trans('user') ?>:</strong> <?= htmlspecialchars($reservation['email']) ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
    <form method="POST" action="/admin/library/loans" class="space-y-6">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <?php if ($reservation): ?>
            <input type="hidden" name="reservation_id" value="<?= (int) $reservation['reservation_id'] ?>">
        <?php endif; ?>

        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('user') ?> *</label>
            <?php if ($user): ?>
                <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                <div class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg">
                    <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?> (<?= htmlspecialchars($user['email']) ?>)
                </div>
            <?php else: ?>
                <select name="user_id" id="user_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value=""><?= trans('select') ?> <?= strtolower(trans('user')) ?>...</option>
                    <?php foreach ($users as $u): ?>
                        <?php if ($u['role'] === 'user'): ?>
                            <option value="<?= (int) $u['user_id'] ?>">
                                <?= htmlspecialchars(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?> (<?= htmlspecialchars($u['email']) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <?php if (isset($errors['user_id'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['user_id']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="physical_book_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('physical_books') ?> *</label>
            <?php if ($physicalBook): ?>
                <input type="hidden" name="physical_book_id" value="<?= (int) $physicalBook['physical_book_id'] ?>">
                <div class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg">
                    <?= htmlspecialchars($physicalBook['title'] ?? '') ?> - #<?= htmlspecialchars($physicalBook['inventory_number'] ?? '') ?> (<?= htmlspecialchars($physicalBook['location'] ?? trans('location_not_provided')) ?>)
                </div>
            <?php else: ?>
                <select name="physical_book_id" id="physical_book_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value=""><?= trans('select') ?> <?= strtolower(trans('physical_books')) ?>...</option>
                    <?php foreach ($physicalBooks as $pb): ?>
                        <?php if ($pb['is_available'] || ($physicalBook && (int) $pb['physical_book_id'] === (int) $physicalBook['physical_book_id'])): ?>
                            <option value="<?= (int) $pb['physical_book_id'] ?>" <?= $physicalBook && (int) $physicalBook['physical_book_id'] === (int) $pb['physical_book_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pb['title']) ?> - #<?= htmlspecialchars($pb['inventory_number']) ?> (<?= htmlspecialchars($pb['location'] ?? trans('location_not_provided')) ?>)
                            </option>
                        <?php elseif (!empty($pb['reserved_user_email'])): ?>
                            <option value="<?= (int) $pb['physical_book_id'] ?>">
                                <?= htmlspecialchars($pb['title']) ?> - #<?= htmlspecialchars($pb['inventory_number']) ?> (<?= htmlspecialchars($pb['location'] ?? trans('location_not_provided')) ?>) — <?= trans('reserved_for_user', ['email' => $pb['reserved_user_email']]) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <?php if (isset($errors['physical_book_id'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['physical_book_id']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('due_date') ?> *</label>
            <input type="date" name="due_date" id="due_date" required value="<?= htmlspecialchars(old('due_date', date('Y-m-d', strtotime('+14 days')))) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <?php if (isset($errors['due_date'])): ?>
                <p class="mt-1 text-sm text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['due_date']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= trans('notes') ?></label>
            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars(old('notes', '')) ?></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition-all"><?= trans('loan_book') ?></button>
            <a href="/admin/library/<?= $reservation ? 'reservations' : 'loans' ?>" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"><?= trans('cancel') ?></a>
        </div>
    </form>
</div>

