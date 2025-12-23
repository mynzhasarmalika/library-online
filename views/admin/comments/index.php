<?php
$comments = $comments ?? [];
$pagination = $pagination ?? ['current_page' => 1, 'last_page' => 1];
?>
<h1 class="text-3xl font-bold text-gray-800 mb-8">Moderate Comments</h1>

<div class="bg-white rounded-lg shadow-sm">
    <div class="divide-y divide-gray-200">
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <?php $avatarUrl = media_url($comment['avatar'] ?? null); ?>
                            <?php if ($avatarUrl): ?>
                                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-10 h-10 rounded-full">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold">
                                    <?= htmlspecialchars(strtoupper(substr($comment['email'] ?? '', 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($comment['first_name'] ?? $comment['email'] ?? 'Unknown') ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars(time_ago($comment['created_at'] ?? '')) ?></p>
                            </div>
                        </div>
                        <form action="/admin/comments/<?= (int) $comment['id'] ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?')">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                        </form>
                    </div>
                    
                    <div class="mb-3">
                        <a href="/books/<?= (int) ($comment['book_id'] ?? 0) ?>" class="text-primary-600 hover:text-primary-700 font-medium">
                            <?= htmlspecialchars($comment['book_title'] ?? 'Unknown Book') ?>
                        </a>
                    </div>
                    
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($comment['comment'] ?? '')) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-12 text-center text-gray-500">
                <p>No comments to moderate.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($pagination['last_page'] > 1): ?>
    <div class="mt-6 flex justify-center gap-2">
        <?php if ($pagination['current_page'] > 1): ?>
            <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
        <?php endif; ?>
        <span class="px-4 py-2">Page <?= $pagination['current_page'] ?> of <?= $pagination['last_page'] ?></span>
        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

