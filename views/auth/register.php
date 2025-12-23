<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6"><?= trans('create_account') ?></h1>
    <form method="POST" action="/register" class="space-y-4">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">

        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('first_name_label') ?></label>
            <input
                id="first_name"
                type="text"
                name="first_name"
                value="<?= htmlspecialchars(old('first_name')) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('last_name_label') ?></label>
            <input
                id="last_name"
                type="text"
                name="last_name"
                value="<?= htmlspecialchars(old('last_name')) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('email_label') ?></label>
            <input
                id="email"
                type="email"
                name="email"
                value="<?= htmlspecialchars(old('email')) ?>"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                required
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('password_label') ?></label>
            <input
                id="password"
                type="password"
                name="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                required
            >
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('confirm_password_label') ?></label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                required
            >
        </div>

        <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700">
            <?= trans('create_account') ?>
        </button>
    </form>
</div>

