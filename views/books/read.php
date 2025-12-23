<?php
$bookId = (int) $book['book_id'];
$bookFile = $book['book_file'] ?? null;
$fileUrl = media_url($bookFile);
$filePath = media_path($bookFile);
$extension = strtolower(pathinfo($bookFile ?? '', PATHINFO_EXTENSION));
$epubHtml = null;
$readerType = null;
if ($fileUrl && $extension === 'pdf') {
    $readerType = 'pdf';
} elseif ($fileUrl && $extension === 'epub') {
    $readerType = 'epub';
    $epubHtml = render_epub_content($book['book_file'] ?? null);
} elseif ($fileUrl && in_array($extension, ['txt', 'html', 'htm'], true)) {
    $readerType = 'text';
}
?>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
    <div class="border-b dark:border-gray-700 p-4 flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($book['title']) ?></h1>
                <p class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($book['author']) ?></p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <?php if ($fileUrl): ?>
                    <a href="/books/<?= $bookId ?>/download" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2" title="<?= trans('download_book') ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <?= trans('download') ?>
                    </a>
                <?php endif; ?>
                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-1">
                    <label class="text-xs text-gray-500 dark:text-gray-400">Zoom</label>
                    <input type="range" min="80" max="180" step="5" value="110" data-reader-zoom class="accent-primary-600 bg-transparent">
                </div>
                <button data-reader-theme class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="<?= trans('toggle_theme') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                <button data-reader-fullscreen class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="Fullscreen">
                    ⤢
                </button>
                <a href="/books/<?= $bookId ?>" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors">← <?= trans('back_to_book') ?></a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 bg-gray-50 dark:bg-gray-900 rounded-xl px-4 py-2" id="readerToolbar">
            <button data-reader-prev class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700 disabled:opacity-50">‹ <?= trans('previous') ?></button>
            <div class="text-sm text-gray-700 dark:text-gray-300">
                <?= trans('page') ?> <span data-reader-page-current>1</span> / <span data-reader-page-total>?</span>
            </div>
            <button data-reader-next class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700 disabled:opacity-50"><?= trans('next') ?> ›</button>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <label for="reader-goto-page" class="whitespace-nowrap"><?= trans('go_to_page') ?>:</label>
                <input 
                    type="number" 
                    id="reader-goto-page" 
                    data-reader-goto-page
                    min="1" 
                    class="w-20 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="№"
                >
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 ml-auto">
                <span><?= trans('progress') ?>:</span>
                <strong data-reader-progress>0%</strong>
            </div>
        </div>
    </div>

    <?php if ($progress): ?>
        <div class="border-b dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= trans('reading_progress') ?></span>
                <span class="text-sm text-gray-600 dark:text-gray-400 font-semibold"><?= number_format((float) ($progress['progress_percentage'] ?? 0), 1) ?>%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full transition-all duration-500" style="width: <?= (float) ($progress['progress_percentage'] ?? 0) ?>%"></div>
            </div>
            <?php if (!empty($progress['last_page'])): ?>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2"><?= trans('last_read_page', ['page' => (int) $progress['last_page']]) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div 
        class="p-4 sm:p-8"
        id="readerContent"
        data-reader-root
        data-reader-type="<?= htmlspecialchars($readerType ?? 'none') ?>"
        data-reader-book-id="<?= $bookId ?>""
        data-reader-initial-page="<?= (int) ($progress['last_page'] ?? 1) ?>"
        data-reader-initial-progress="<?= (float) ($progress['progress_percentage'] ?? 0) ?>"
        data-reader-pdf="<?= $readerType === 'pdf' ? htmlspecialchars($fileUrl) : '' ?>"
    >
        <?php if (!$readerType): ?>
            <?php if ($fileUrl): ?>
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4"><?= trans('unsupported_format_download') ?></p>
                    <a href="/books/<?= $bookId ?>/download" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 inline-block shadow-md hover:shadow-lg transition-all"><?= trans('download_file') ?></a>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4"><?= trans('no_book_file_available') ?></p>
                    <a href="/books/<?= $bookId ?>" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">← <?= trans('back_to_book') ?></a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($readerType === 'pdf'): ?>
                <div class="reader-stage bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <div class="book-viewer-container" id="bookViewerContainer">
                        <div class="book-pages-wrapper" id="bookPagesWrapper">
                            <div class="book-page left-page" id="leftPage">
                                <canvas id="pdfCanvasLeft" class="book-page-canvas"></canvas>
                            </div>
                            <div class="book-page right-page" id="rightPage">
                                <canvas id="pdfCanvasRight" class="book-page-canvas"></canvas>
                            </div>
                        </div>
                        <div class="book-spine"></div>
                    </div>
                </div>
            <?php elseif ($readerType === 'text'): ?>
                <?php if ($filePath && is_file($filePath)): ?>
                    <div class="reader-stage">
                        <div id="textPagerViewport" class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-inner overflow-hidden">
                            <div id="textPagerContent" data-reader-text class="p-8 font-serif text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap">
                                <?= htmlspecialchars(file_get_contents($filePath)) ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 dark:text-gray-400"><?= trans('file_not_found') ?></p>
                <?php endif; ?>
            <?php elseif ($readerType === 'epub'): ?>
                <div class="reader-stage">
                    <?php if ($epubHtml): ?>
                        <div id="textPagerViewport" class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-inner overflow-hidden">
                            <div id="textPagerContent" data-reader-text class="p-8 prose prose-lg dark:prose-invert max-w-none">
                                <?= $epubHtml ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-white dark:bg-gray-900 p-8 rounded-lg shadow-inner border border-gray-200 dark:border-gray-700 text-center text-gray-600 dark:text-gray-300">
                            <?= trans('epub_unable_to_render') ?>
                        </div>
                    <?php endif; ?>
                    <div class="text-right mt-4">
                        <a href="/books/<?= $bookId ?>/download" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 shadow-md hover:shadow-lg transition">
                            <?= trans('download_epub') ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

<?php if ($readerType === 'pdf'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.worker.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initBookReader();
});
</script>

<style>
#textPagerViewport {
    height: min(75vh, 900px);
    position: relative;
    overflow: hidden;
}
#textPagerContent {
    position: relative;
    transition: transform 0.3s ease;
    will-change: transform;
}
.reader-stage {
    min-height: 60vh;
    max-height: 80vh;
}
[data-reader-text] {
    font-size: 100%;
    transition: font-size 0.2s ease;
}
.reader-dark [data-reader-text] {
    background-color: #111827;
    color: #f9fafb;
}

/* Book viewer styles */
.book-viewer-container {
    position: relative;
    width: 100%;
    min-height: 70vh;
    display: flex;
    justify-content: center;
    align-items: center;
    perspective: 2500px;
    perspective-origin: center center;
    padding: 2rem;
    transform-style: preserve-3d;
}

.book-pages-wrapper {
    position: relative;
    display: flex;
    gap: 0;
    transform-style: preserve-3d;
}

.book-page {
    position: relative;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    will-change: transform;
}

.book-page.left-page {
    border-radius: 8px 0 0 8px;
    border-right: 1px solid #e5e7eb;
}

.book-page.right-page {
    border-radius: 0 8px 8px 0;
    border-left: 1px solid #e5e7eb;
}

.book-page-canvas {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 45vw;
    max-height: 75vh;
    object-fit: contain;
}

.book-spine {
    display: none; /* Убрана центральная линия */
}

.dark .book-page {
    background: #1f2937;
}

.dark .book-page.left-page {
    border-right-color: #374151;
}

.dark .book-page.right-page {
    border-left-color: #374151;
}

.dark .book-spine {
    background: linear-gradient(to right, #374151, #4b5563, #374151);
}

.book-page-canvas {
    transition: opacity 0.2s ease;
}

.dark .book-page-flip {
    background: #111827;
}

/* Полноэкранный режим - простая версия */
:fullscreen,
:-webkit-full-screen,
:-moz-full-screen,
:-ms-fullscreen {
    background: #1f2937 !important;
}

:fullscreen #readerContent,
:-webkit-full-screen #readerContent,
:-moz-full-screen #readerContent,
:-ms-fullscreen #readerContent {
    padding: 0 !important;
    margin: 0 !important;
    height: 100vh !important;
    width: 100vw !important;
    overflow: hidden !important;
    background: #1f2937 !important;
}

:fullscreen body,
:-webkit-full-screen body,
:-moz-full-screen body,
:-ms-fullscreen body {
    overflow: hidden !important;
}

/* Скрываем верхнюю панель в полноэкранном режиме */
:fullscreen .border-b > div:first-child,
:-webkit-full-screen .border-b > div:first-child,
:-moz-full-screen .border-b > div:first-child,
:-ms-fullscreen .border-b > div:first-child {
    display: none !important;
}

:fullscreen .reader-stage,
:-webkit-full-screen .reader-stage,
:-moz-full-screen .reader-stage,
:-ms-fullscreen .reader-stage {
    min-height: 100vh !important;
    max-height: 100vh !important;
    height: 100vh !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    border: none !important;
    background: transparent !important;
}

:fullscreen .book-viewer-container,
:-webkit-full-screen .book-viewer-container,
:-moz-full-screen .book-viewer-container,
:-ms-fullscreen .book-viewer-container,
.book-viewer-container.fullscreen-mode {
    width: 100% !important;
    height: 100% !important;
    min-height: 100vh !important;
    max-height: 100vh !important;
    padding: 2rem !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    box-sizing: border-box !important;
}

:fullscreen .book-pages-wrapper,
:-webkit-full-screen .book-pages-wrapper,
:-moz-full-screen .book-pages-wrapper,
:-ms-fullscreen .book-pages-wrapper {
    height: auto !important;
    max-height: 100vh !important;
    width: auto !important;
    max-width: 90vw !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 0 !important;
}

:fullscreen .book-page-canvas,
:-webkit-full-screen .book-page-canvas,
:-moz-full-screen .book-page-canvas,
:-ms-fullscreen .book-page-canvas {
    max-width: 45vw !important;
    max-height: 100vh !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain !important;
}

/* Показываем панель инструментов внизу в полноэкранном режиме */
:fullscreen #readerToolbar,
:-webkit-full-screen #readerToolbar,
:-moz-full-screen #readerToolbar,
:-ms-fullscreen #readerToolbar {
    display: flex !important;
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    background: rgba(31, 41, 55, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border-radius: 0 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    padding: 1rem !important;
    z-index: 10000 !important;
    justify-content: center !important;
    flex-wrap: wrap !important;
    gap: 0.75rem !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3) !important;
}

/* Стили для элементов панели инструментов в полноэкранном режиме */
:fullscreen #readerToolbar button,
:-webkit-full-screen #readerToolbar button,
:-moz-full-screen #readerToolbar button,
:-ms-fullscreen #readerToolbar button,
:fullscreen #readerToolbar input,
:-webkit-full-screen #readerToolbar input,
:-moz-full-screen #readerToolbar input,
:-ms-fullscreen #readerToolbar input {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    color: #f9fafb !important;
}

:fullscreen #readerToolbar button:hover,
:-webkit-full-screen #readerToolbar button:hover,
:-moz-full-screen #readerToolbar button:hover,
:-ms-fullscreen #readerToolbar button:hover {
    background: rgba(255, 255, 255, 0.2) !important;
}

:fullscreen #readerToolbar label,
:-webkit-full-screen #readerToolbar label,
:-moz-full-screen #readerToolbar label,
:-ms-fullscreen #readerToolbar label,
:fullscreen #readerToolbar span,
:-webkit-full-screen #readerToolbar span,
:-moz-full-screen #readerToolbar span,
:-ms-fullscreen #readerToolbar span,
:fullscreen #readerToolbar strong,
:-webkit-full-screen #readerToolbar strong,
:-moz-full-screen #readerToolbar strong,
:-ms-fullscreen #readerToolbar strong {
    color: #f9fafb !important;
}

/* Показываем панель прогресса в полноэкранном режиме */
:fullscreen .bg-gray-50.dark\:bg-gray-900,
:-webkit-full-screen .bg-gray-50.dark\:bg-gray-900,
:-moz-full-screen .bg-gray-50.dark\:bg-gray-900,
:-ms-fullscreen .bg-gray-50.dark\:bg-gray-900 {
    display: block !important;
    position: fixed !important;
    bottom: 80px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: auto !important;
    min-width: 300px !important;
    max-width: 90% !important;
    background: rgba(31, 41, 55, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 0.5rem !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    padding: 1rem !important;
    z-index: 9999 !important;
}

/* Скрываем навигацию и футер в полноэкранном режиме */
:fullscreen nav,
:-webkit-full-screen nav,
:-moz-full-screen nav,
:-ms-fullscreen nav,
:fullscreen footer,
:-webkit-full-screen footer,
:-moz-full-screen footer,
:-ms-fullscreen footer {
    display: none !important;
}

@media (max-width: 768px) {
    .book-pages-wrapper {
        flex-direction: column;
        gap: 1rem;
    }
    
    .book-page.left-page,
    .book-page.right-page {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .book-page-canvas {
        max-width: 90vw;
    }
    
    .book-spine {
        display: none;
    }
    
    :fullscreen .book-page-canvas,
    :-webkit-full-screen .book-page-canvas,
    :-moz-full-screen .book-page-canvas,
    :-ms-fullscreen .book-page-canvas {
        max-width: 90vw !important;
    }
}
</style>
