<?php
$user = auth();
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$avatarUrl = media_url($user['avatar'] ?? null);
?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Profile</h1>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <!-- Profile Photo -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
            <div class="flex items-center gap-4">
                <?php if ($avatarUrl): ?>
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-24 h-24 rounded-full object-cover">
                <?php else: ?>
                    <?php 
                    $userRole = strtolower($user['role'] ?? 'user');
                    $avatarBg = get_role_color_classes($userRole, 'bg');
                    ?>
                    <div class="w-24 h-24 rounded-full <?= $avatarBg ?> flex items-center justify-center text-white text-3xl font-semibold">
                        <?= htmlspecialchars(strtoupper(substr($user['email'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Profile Form -->
        <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="_method" value="PUT">

            <div>
                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Change Avatar</label>
                <input 
                    type="file" 
                    id="avatar" 
                    name="avatar" 
                    accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                >
                <?php if (isset($errors['avatar'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['avatar']) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        value="<?= htmlspecialchars(old('first_name', $user['first_name'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['first_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        value="<?= htmlspecialchars(old('last_name', $user['last_name'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['last_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars(old('email', $user['email'] ?? '')) ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <button 
                type="submit" 
                class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium"
            >
                Update Profile
            </button>
        </form>

        <!-- Change Password -->
        <div class="mt-12 pt-8 border-t">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Change Password</h2>
            <form method="POST" action="/profile/password" class="space-y-6">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="_method" value="PUT">

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['current_password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['current_password']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['password_confirmation']) ?></p>
                    <?php endif; ?>
                </div>

                <button 
                    type="submit" 
                    class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium"
                >
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

