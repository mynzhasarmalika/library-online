// Main application JavaScript

// Toast notification system
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const toast = document.createElement('div');
    const bgColor = type === 'success' 
        ? 'bg-green-500 dark:bg-green-600' 
        : type === 'error' 
        ? 'bg-red-500 dark:bg-red-600'
        : 'bg-blue-500 dark:bg-blue-600';
    
    const icon = type === 'success' 
        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        : type === 'error'
        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
        : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
    
    toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] max-w-md animate-slide-up mb-2`;
    toast.innerHTML = `
        ${icon}
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Dropdown menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown menus
    document.querySelectorAll('[data-dropdown]').forEach(function(wrapper) {
        const button = wrapper.querySelector('[data-dropdown-button]');
        const menu = wrapper.querySelector('[data-dropdown-menu]');

        button?.addEventListener('click', function(event) {
            event.stopPropagation();
            menu?.classList.toggle('hidden');
        });
    });

    // Close dropdowns on outside click
    window.addEventListener('click', function() {
        document.querySelectorAll('[data-dropdown-menu]').forEach(function(menu) {
            menu.classList.add('hidden');
        });
    });
});

// Favorite button handler
function initFavoriteButton(buttonId, bookId, csrfToken) {
    const favBtn = document.getElementById(buttonId);
    if (!favBtn) return;

    favBtn.addEventListener('click', function() {
        const text = document.getElementById('favoriteText');
        if (!text) return;
        
        const originalText = text.textContent;
        const originalClassName = favBtn.className;
        favBtn.disabled = true;
        favBtn.style.opacity = '0.6';
        favBtn.style.cursor = 'wait';
        
        const endpoint = favBtn.dataset.endpoint || `/books/${bookId}/favorite`;
        
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: `_token=${encodeURIComponent(csrfToken)}`
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    const errorMsg = favBtn.dataset.errorText || data.message || 'Не удалось обновить избранное. Попробуйте снова.';
                    showToast(errorMsg, 'error');
                    favBtn.className = originalClassName;
                    text.textContent = originalText;
                    favBtn.disabled = false;
                    favBtn.style.opacity = '1';
                    favBtn.style.cursor = 'pointer';
                    return;
                }
                
                const favorited = data.isFavorite;
                
                if (favorited) {
                    favBtn.className = 'px-6 py-2 rounded-lg border-2 border-red-500 text-red-600 bg-red-50 dark:bg-red-900/20 dark:border-red-400 dark:text-red-400 font-medium transition-all shadow-md';
                    const favoritedText = favBtn.dataset.favoritedText || '❤️ В избранном';
                    text.textContent = favoritedText;
                    const addedToast = favBtn.dataset.addedToast || 'Книга добавлена в избранное';
                    showToast(addedToast, 'success');
                } else {
                    favBtn.className = 'px-6 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-red-500 hover:text-red-600 dark:hover:border-red-400 dark:hover:text-red-400 font-medium transition-all';
                    const addToFavText = favBtn.dataset.addToFavText || '🤍 Добавить в избранное';
                    text.textContent = addToFavText;
                    const removedToast = favBtn.dataset.removedToast || 'Книга удалена из избранного';
                    showToast(removedToast, 'success');
                }
                favBtn.disabled = false;
                favBtn.style.opacity = '1';
                favBtn.style.cursor = 'pointer';
            })
            .catch(error => {
                console.error('Error:', error);
                const errorText = favBtn.dataset.errorText || 'Произошла ошибка. Попробуйте снова.';
                showToast(errorText, 'error');
                favBtn.className = originalClassName;
                text.textContent = originalText;
                favBtn.disabled = false;
                favBtn.style.opacity = '1';
                favBtn.style.cursor = 'pointer';
            });
    });
}

// Progress form handler
function initProgressForm(formId, bookId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        }
        
        const formData = new FormData(this);
        const params = new URLSearchParams();
        formData.forEach((value, key) => {
            params.append(key, value);
        });
        
        fetch(`/books/${bookId}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Progress updated successfully', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('Error updating progress', 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
    });
}

// Rating form handler (submit on star click via AJAX)
function initRatingForm(formSelector) {
    const forms = document.querySelectorAll(formSelector);
    forms.forEach(form => {
        const buttons = form.querySelectorAll('button[type="submit"]');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const score = parseInt(button.value || '0', 10);
                if (score < 1 || score > 5) {
                    return;
                }
                
                const form = button.closest('form');
                if (!form) return;
                
                const csrfToken = form.querySelector('input[name="_token"]')?.value || '';
                const action = form.action || '';
                
                // Disable all buttons
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                });
                
                const formData = new URLSearchParams();
                formData.append('_token', csrfToken);
                formData.append('score', score.toString());
                
                fetch(action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData.toString()
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok');
                })
                .then(() => {
                    // Update star display
                    buttons.forEach((btn, index) => {
                        const btnScore = parseInt(btn.value || '0', 10);
                        if (btnScore <= score) {
                            btn.classList.remove('text-gray-300', 'dark:text-gray-600');
                            btn.classList.add('text-yellow-400');
                        } else {
                            btn.classList.remove('text-yellow-400');
                            btn.classList.add('text-gray-300', 'dark:text-gray-600');
                        }
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    });
                    
                    // Update rating text
                    const ratingText = form.querySelector('span');
                    if (ratingText) {
                        const starText = score === 1 ? 'star' : 'stars';
                        ratingText.textContent = `You rated this ${score} ${starText}`;
                        if (!ratingText.parentElement) {
                            form.appendChild(document.createTextNode(' '));
                            form.appendChild(ratingText);
                        }
                    }
                    
                    showToast('Rating saved successfully!', 'success');
                })
                .catch(error => {
                    console.error('Error saving rating:', error);
                    showToast('Error saving rating. Please try again.', 'error');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    });
                });
            });
        });
    });
}

// Auto-submit filter forms
function initAutoSubmitForms() {
    document.querySelectorAll('form[data-auto-submit]').forEach(form => {
        const inputs = form.querySelectorAll('input[type="checkbox"], select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                form.submit();
            });
        });
    });
}

// Search autocomplete
function initSearchAutocomplete() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    let timeout;
    const resultsContainer = document.createElement('div');
    resultsContainer.id = 'searchResults';
    resultsContainer.className = 'absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto hidden';
    searchInput.parentElement.style.position = 'relative';
    searchInput.parentElement.appendChild(resultsContainer);

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(timeout);
        
        if (query.length < 2) {
            resultsContainer.classList.add('hidden');
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/api/search/autocomplete?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsContainer.classList.add('hidden');
                        return;
                    }

                    resultsContainer.innerHTML = data.map(book => `
                        <a href="/books/${book.book_id}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-900 border-b dark:border-gray-700 last:border-0">
                            <div class="font-semibold text-gray-800 dark:text-gray-100">${escapeHtml(book.title)}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">${escapeHtml(book.author)}</div>
                        </a>
                    `).join('');
                    resultsContainer.classList.remove('hidden');
                })
                .catch(() => {
                    resultsContainer.classList.add('hidden');
                });
        }, 300);
    });

    // Hide on outside click
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            resultsContainer.classList.add('hidden');
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initAutoSubmitForms();
    initRatingForm('form[data-rating-form]');
    initSearchAutocomplete();
    initNotificationForms();
});

// Notifications mark-as-read handlers
function initNotificationForms() {
    // "Mark all as read" via AJAX
    const markAllForm = document.getElementById('markAllNotificationsForm');
    if (markAllForm) {
        markAllForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitBtn = markAllForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            const successMessage = markAllForm.dataset.successMessage || 'All notifications marked as read';
            const errorMessage = markAllForm.dataset.errorMessage || 'Failed to mark notifications as read';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = '...';
            }

            const formData = new FormData(markAllForm);
            const params = new URLSearchParams();
            formData.forEach((value, key) => {
                params.append(key, value);
            });

            fetch(markAllForm.action || '/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: params.toString()
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        showToast(errorMessage, 'error');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }
                        return;
                    }

                    // Update UI: remove unread styles and "New" badges
                    document.querySelectorAll('[data-notification-unread]').forEach(card => {
                        card.removeAttribute('data-notification-unread');
                        card.classList.remove('border-l-4', 'border-l-primary-600');
                        const badge = card.querySelector('.notification-badge-new');
                        if (badge) {
                            badge.remove();
                        }
                        const singleForm = card.querySelector('form.notification-mark-read-form');
                        if (singleForm) {
                            singleForm.remove();
                        }
                    });

                    if (submitBtn) {
                        submitBtn.remove();
                    }

                    showToast(successMessage, 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(errorMessage, 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
        });
    }

    // Single notification "mark as read" via AJAX
    document.querySelectorAll('form.notification-mark-read-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            const successMessage = form.dataset.successMessage || 'Notification marked as read';
            const errorMessage = form.dataset.errorMessage || 'Failed to mark notification as read';
            const notificationId = form.dataset.notificationId;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = '...';
            }

            const formData = new FormData(form);
            const params = new URLSearchParams();
            formData.forEach((value, key) => {
                params.append(key, value);
            });

            fetch(form.action || `/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: params.toString()
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        showToast(errorMessage, 'error');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }
                        return;
                    }

                    const card = document.getElementById(`notification-${notificationId}`);
                    if (card) {
                        card.classList.remove('border-l-4', 'border-l-primary-600');
                        card.removeAttribute('data-notification-unread');
                        const badge = card.querySelector('.notification-badge-new');
                        if (badge) {
                            badge.remove();
                        }
                    }

                    form.remove();
                    showToast(successMessage, 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(errorMessage, 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                });
        });
    });
}

// Generic debounce helper
function debounce(fn, wait = 200) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(null, args), wait);
    };
}

// Reading experience
function initBookReader() {
    const root = document.querySelector('[data-reader-root]');
    if (!root) {
        return;
    }

    const readerType = root.dataset.readerType;
    if (!readerType || readerType === 'none') {
        return;
    }

    const bookId = parseInt(root.dataset.readerBookId || '0', 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const initialPage = Math.max(1, parseInt(root.dataset.readerInitialPage || '1', 10));

    const prevBtn = document.querySelector('[data-reader-prev]');
    const nextBtn = document.querySelector('[data-reader-next]');
    const pageCurrent = document.querySelector('[data-reader-page-current]');
    const pageTotal = document.querySelector('[data-reader-page-total]');
    const progressDisplay = document.querySelector('[data-reader-progress]');
    const zoomRange = document.querySelector('[data-reader-zoom]');
    const themeToggle = document.querySelector('[data-reader-theme]');
    const fullscreenBtn = document.querySelector('[data-reader-fullscreen]');
    const contentWrapper = document.getElementById('readerContent');

    let currentPage = initialPage;
    let totalPages = 1;
    let pdfDoc = null;
    let pdfRenderTasks = { left: null, right: null };
    let zoomValue = parseInt(localStorage.getItem('readerZoom') || zoomRange?.value || '110', 10);
    let readerTheme = localStorage.getItem('readerTheme') || 'light';
    let progressTimer = null;
    let isFlipping = false;
    let touchStartX = 0;
    let touchStartY = 0;

    // PDF-specific elements
    const leftCanvas = document.getElementById('pdfCanvasLeft');
    const rightCanvas = document.getElementById('pdfCanvasRight');
    const leftPage = document.getElementById('leftPage');
    const rightPage = document.getElementById('rightPage');
    const pageFlip = document.getElementById('pageFlip');
    const bookViewerContainer = document.getElementById('bookViewerContainer');

    function updateTheme() {
        if (!contentWrapper) return;
        if (readerTheme === 'dark') {
            contentWrapper.classList.add('reader-dark');
            document.documentElement.classList.add('dark');
        } else {
            contentWrapper.classList.remove('reader-dark');
            document.documentElement.classList.remove('dark');
        }
    }

    function saveProgress() {
        if (!bookId || !csrfToken) return;
        const percentage = totalPages ? Math.min(100, (currentPage / totalPages) * 100) : 0;
        const status = percentage >= 99 ? 'completed' : 'reading';
        const body = `_token=${encodeURIComponent(csrfToken)}&last_page=${currentPage}&progress_percentage=${percentage.toFixed(2)}&status=${status}`;
        fetch(`/books/${bookId}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body
        }).then(response => {
            if (response.ok && progressDisplay) {
                progressDisplay.textContent = `${percentage.toFixed(1)}%`;
            }
        }).catch(() => {
            /* ignore network errors */
        });
    }

    function scheduleProgressSave() {
        if (progressTimer) {
            clearTimeout(progressTimer);
        }
        progressTimer = setTimeout(saveProgress, 800);
    }

    function updatePageMeta() {
        if (pageCurrent) {
            pageCurrent.textContent = currentPage;
        }
        if (pageTotal) {
            pageTotal.textContent = totalPages || '?';
        }
        const percentage = totalPages ? Math.min(100, (currentPage / totalPages) * 100) : 0;
        if (progressDisplay) {
            progressDisplay.textContent = `${percentage.toFixed(1)}%`;
        }
        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
        }
    }

    function applyZoom(newZoom) {
        zoomValue = newZoom;
        localStorage.setItem('readerZoom', zoomValue.toString());
        if (readerType === 'pdf') {
            renderPdfPages();
        }
    }

    function toggleTheme() {
        readerTheme = readerTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('readerTheme', readerTheme);
        updateTheme();
    }

    function toggleFullscreen() {
        if (!contentWrapper) return;
        if (!document.fullscreenElement && 
            !document.webkitFullscreenElement && 
            !document.mozFullScreenElement && 
            !document.msFullscreenElement) {
            // Входим в полноэкранный режим
            const element = contentWrapper.requestFullscreen?.() || 
                          contentWrapper.webkitRequestFullscreen?.() || 
                          contentWrapper.mozRequestFullScreen?.() || 
                          contentWrapper.msRequestFullscreen?.();
            
            // Добавляем класс для полноэкранного режима
            if (element) {
                element.then(() => {
                    document.body.classList.add('fullscreen-active');
                    if (bookViewerContainer) {
                        bookViewerContainer.classList.add('fullscreen-mode');
                    }
                    // Перерисовываем страницы для корректного отображения
                    if (readerType === 'pdf') {
                        setTimeout(() => renderPdfPages(), 200);
                    }
                }).catch(err => {
                    console.error('Error entering fullscreen:', err);
                });
            }
        } else {
            // Выходим из полноэкранного режима
            const exitPromise = document.exitFullscreen?.() || 
            document.webkitExitFullscreen?.() || 
            document.mozCancelFullScreen?.() || 
            document.msExitFullscreen?.();
            
            if (exitPromise) {
                exitPromise.then(() => {
                    document.body.classList.remove('fullscreen-active');
                    if (bookViewerContainer) {
                        bookViewerContainer.classList.remove('fullscreen-mode');
                    }
                    // Перерисовываем страницы
                    if (readerType === 'pdf') {
                        setTimeout(() => renderPdfPages(), 200);
                    }
                }).catch(err => {
                    console.error('Error exiting fullscreen:', err);
                });
            } else {
                document.body.classList.remove('fullscreen-active');
                if (bookViewerContainer) {
                    bookViewerContainer.classList.remove('fullscreen-mode');
                }
            }
        }
    }
    
    // Обработчик изменения полноэкранного режима
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            document.body.classList.remove('fullscreen-active');
            if (bookViewerContainer) {
                bookViewerContainer.classList.remove('fullscreen-mode');
            }
            if (readerType === 'pdf') {
                setTimeout(() => renderPdfPages(), 200);
            }
        }
    });
    
    document.addEventListener('webkitfullscreenchange', () => {
        if (!document.webkitFullscreenElement) {
            document.body.classList.remove('fullscreen-active');
            if (bookViewerContainer) {
                bookViewerContainer.classList.remove('fullscreen-mode');
            }
            if (readerType === 'pdf') {
                setTimeout(() => renderPdfPages(), 200);
            }
        }
    });
    
    document.addEventListener('mozfullscreenchange', () => {
        if (!document.mozFullScreenElement) {
            document.body.classList.remove('fullscreen-active');
            if (bookViewerContainer) {
                bookViewerContainer.classList.remove('fullscreen-mode');
            }
            if (readerType === 'pdf') {
                setTimeout(() => renderPdfPages(), 200);
            }
        }
    });
    
    document.addEventListener('MSFullscreenChange', () => {
        if (!document.msFullscreenElement) {
            document.body.classList.remove('fullscreen-active');
            if (bookViewerContainer) {
                bookViewerContainer.classList.remove('fullscreen-mode');
            }
            if (readerType === 'pdf') {
                setTimeout(() => renderPdfPages(), 200);
            }
        }
    });

    function goToNextPage() {
        if (isFlipping || currentPage >= totalPages) return;
        isFlipping = true;
        
        const nextPage = Math.min(currentPage + 2, totalPages);
        
        // Simple fade out
        if (leftCanvas) leftCanvas.style.opacity = '0.3';
        if (rightCanvas) rightCanvas.style.opacity = '0.3';
        
        // Change page and render
        currentPage = nextPage;
        renderPdfPages(false);
        
        // Fade in after short delay
        setTimeout(() => {
            if (leftCanvas) leftCanvas.style.opacity = '1';
            if (rightCanvas) rightCanvas.style.opacity = '1';
            isFlipping = false;
        }, 150);
    }

    function goToPage(pageNum) {
        if (isFlipping || !pdfDoc) return;
        const targetPage = Math.max(1, Math.min(pageNum, totalPages));
        if (targetPage === currentPage) return;
        
        isFlipping = true;
        
        // Simple fade out
        if (leftCanvas) leftCanvas.style.opacity = '0.3';
        if (rightCanvas) rightCanvas.style.opacity = '0.3';
        
        // Change page and render
        currentPage = targetPage;
        renderPdfPages(false);
        
        // Fade in after short delay
        setTimeout(() => {
            if (leftCanvas) leftCanvas.style.opacity = '1';
            if (rightCanvas) rightCanvas.style.opacity = '1';
            isFlipping = false;
        }, 150);
    }
    
    function goToPrevPage() {
        if (isFlipping || currentPage <= 1) return;
        isFlipping = true;
        
        const prevPage = Math.max(currentPage - 2, 1);
        
        // Simple fade out
        if (leftCanvas) leftCanvas.style.opacity = '0.3';
        if (rightCanvas) rightCanvas.style.opacity = '0.3';
        
        // Change page and render
        currentPage = prevPage;
        renderPdfPages(false);
        
        // Fade in after short delay
        setTimeout(() => {
            if (leftCanvas) leftCanvas.style.opacity = '1';
            if (rightCanvas) rightCanvas.style.opacity = '1';
            isFlipping = false;
        }, 150);
    }

    function renderPdfPages() {
        if (!pdfDoc || !leftCanvas || !rightCanvas) return;
        
        let leftPageNum = Math.floor((currentPage - 1) / 2) * 2 + 1;
        let rightPageNum = leftPageNum + 1;
        
        leftPageNum = Math.max(1, Math.min(leftPageNum, totalPages));
        rightPageNum = Math.max(1, Math.min(rightPageNum, totalPages));
        
        // Cancel any ongoing renders
        if (pdfRenderTasks.left) {
            pdfRenderTasks.left.cancel();
        }
        if (pdfRenderTasks.right) {
            pdfRenderTasks.right.cancel();
        }
        
        // Вычисляем масштаб на основе zoomValue (zoomValue в процентах, например 110 = 110%)
        // Базовый масштаб 1.0 соответствует 100%, поэтому делим на 100
        const baseScale = 1.0;
        const scale = baseScale * (zoomValue / 100);
        
        // Render left page
        if (leftPageNum <= totalPages) {
            leftCanvas.style.opacity = '0.5';
            pdfDoc.getPage(leftPageNum).then(page => {
                // Получаем viewport с нужным масштабом - это масштабирует саму страницу PDF
                const viewport = page.getViewport({ scale });
                // Устанавливаем размер canvas равным размеру отрендеренной страницы
                leftCanvas.width = viewport.width;
                leftCanvas.height = viewport.height;
                const ctx = leftCanvas.getContext('2d');
                // Рендерим страницу с этим viewport - страница будет отрендерена в увеличенном/уменьшенном размере
                pdfRenderTasks.left = page.render({ canvasContext: ctx, viewport });
                return pdfRenderTasks.left.promise;
            }).then(() => {
                leftCanvas.style.opacity = '1';
            }).catch((error) => {
                if (error.name !== 'RenderingCancelledException') {
                    console.error('Error rendering left page:', error);
                }
                leftCanvas.style.opacity = '1';
            });
        } else {
            const ctx = leftCanvas.getContext('2d');
            ctx.clearRect(0, 0, leftCanvas.width, leftCanvas.height);
            leftCanvas.style.opacity = '1';
        }
        
        // Render right page
        if (rightPageNum <= totalPages && rightPageNum !== leftPageNum) {
            rightCanvas.style.opacity = '0.5';
            pdfDoc.getPage(rightPageNum).then(page => {
                // Получаем viewport с нужным масштабом - это масштабирует саму страницу PDF
                const viewport = page.getViewport({ scale });
                // Устанавливаем размер canvas равным размеру отрендеренной страницы
                rightCanvas.width = viewport.width;
                rightCanvas.height = viewport.height;
                const ctx = rightCanvas.getContext('2d');
                // Рендерим страницу с этим viewport - страница будет отрендерена в увеличенном/уменьшенном размере
                pdfRenderTasks.right = page.render({ canvasContext: ctx, viewport });
                return pdfRenderTasks.right.promise;
            }).then(() => {
                rightCanvas.style.opacity = '1';
            }).catch((error) => {
                if (error.name !== 'RenderingCancelledException') {
                    console.error('Error rendering right page:', error);
                }
                rightCanvas.style.opacity = '1';
            });
        } else {
            const ctx = rightCanvas.getContext('2d');
            ctx.clearRect(0, 0, rightCanvas.width, rightCanvas.height);
            rightCanvas.style.opacity = '1';
        }
        
        updatePageMeta();
        scheduleProgressSave();
    }



    const recalcTextPagination = debounce(() => {
        if (readerType === 'pdf') return;
        const viewport = document.getElementById('textPagerViewport');
        const content = document.getElementById('textPagerContent');
        if (!viewport || !content) {
            return;
        }
        const viewportHeight = viewport.clientHeight;
        if (!viewportHeight) {
            return;
        }
        totalPages = Math.max(1, Math.ceil(content.scrollHeight / viewportHeight));
        const clampedPage = Math.min(Math.max(1, currentPage), totalPages);
        currentPage = clampedPage;
        const offset = (currentPage - 1) * viewportHeight;
        content.style.transform = `translateY(-${offset}px)`;
        updatePageMeta();
    }, 200);

    function initPdfReader() {
        const pdfUrl = root.dataset.readerPdf;
        if (!pdfUrl || !window.pdfjsLib) {
            console.error('PDF.js library not loaded or PDF URL not provided');
            return;
        }
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = window.pdfjsLib.GlobalWorkerOptions.workerSrc ||
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.worker.min.js';
        
        // Show loading indicator
        const container = bookViewerContainer || root;
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'pdfLoading';
        loadingDiv.className = 'text-center py-12 text-gray-600 dark:text-gray-400';
        loadingDiv.innerHTML = '<div class="animate-spin inline-block w-8 h-8 border-4 border-primary-600 border-t-transparent rounded-full"></div><p class="mt-4">Загрузка PDF...</p>';
        container.appendChild(loadingDiv);
        
        window.pdfjsLib.getDocument({
            url: pdfUrl,
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/cmaps/',
            cMapPacked: true,
        }).promise.then(doc => {
            pdfDoc = doc;
            totalPages = doc.numPages;
            updatePageMeta();
            const loadingDiv = document.getElementById('pdfLoading');
            if (loadingDiv) {
                loadingDiv.remove();
            }
            renderPdfPages();
        }).catch(error => {
            console.error('Unable to load PDF:', error);
            const loadingDiv = document.getElementById('pdfLoading');
            if (loadingDiv) {
                loadingDiv.innerHTML = '<p class="text-red-600 dark:text-red-400">Ошибка загрузки PDF. Попробуйте снова или скачайте файл.</p>';
            }
        });
    }

    function initTextReader() {
        recalcTextPagination();
        window.addEventListener('resize', recalcTextPagination);
    }

    // Event listeners
    prevBtn?.addEventListener('click', () => {
        if (readerType === 'pdf') {
            goToPrevPage();
        } else {
            currentPage = Math.max(1, currentPage - 1);
            recalcTextPagination();
        }
    });

    nextBtn?.addEventListener('click', () => {
        if (readerType === 'pdf') {
            goToNextPage();
        } else {
            currentPage = Math.min(totalPages, currentPage + 1);
            recalcTextPagination();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', event => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName || '')) {
            return;
        }
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            if (readerType === 'pdf') {
                goToNextPage();
            } else {
                nextBtn?.click();
            }
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            if (readerType === 'pdf') {
                goToPrevPage();
            } else {
                prevBtn?.click();
            }
        }
    });

    // Click on pages to flip
    if (leftPage) {
        leftPage.addEventListener('click', (e) => {
            const rect = leftPage.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            if (clickX < rect.width / 3) {
                goToPrevPage();
            }
        });
    }

    if (rightPage) {
        rightPage.addEventListener('click', (e) => {
            const rect = rightPage.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            if (clickX > rect.width * 2 / 3) {
                goToNextPage();
            }
        });
    }

    // Swipe support
    if (bookViewerContainer) {
        bookViewerContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        bookViewerContainer.addEventListener('touchend', (e) => {
            if (!touchStartX || !touchStartY) return;
            
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const diffX = touchStartX - touchEndX;
            const diffY = touchStartY - touchEndY;
            
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    goToNextPage();
                } else {
                    goToPrevPage();
                }
            }
            
            touchStartX = 0;
            touchStartY = 0;
        }, { passive: true });
    }

    if (zoomRange) {
        zoomRange.value = zoomValue;
        zoomRange.addEventListener('input', () => applyZoom(parseInt(zoomRange.value, 10)));
    }

    // Go to page input
    const gotoPageInput = document.querySelector('[data-reader-goto-page]');
    if (gotoPageInput) {
        const updateGotoMax = () => {
            if (totalPages > 0) {
                gotoPageInput.setAttribute('max', totalPages);
            }
        };
        
        gotoPageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const pageNum = parseInt(gotoPageInput.value, 10);
                if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= totalPages) {
                    goToPage(pageNum);
                    gotoPageInput.value = '';
                } else {
                    gotoPageInput.value = '';
                    showToast(`Please enter a page number between 1 and ${totalPages}`, 'error');
                }
            }
        });
        
        // Обновляем максимальное значение при изменении totalPages
        updateGotoMax();
        
        // Переопределяем updatePageMeta для обновления max атрибута
        const originalUpdatePageMeta = updatePageMeta;
        updatePageMeta = function() {
            originalUpdatePageMeta();
            updateGotoMax();
        };
    }

    themeToggle?.addEventListener('click', toggleTheme);
    fullscreenBtn?.addEventListener('click', toggleFullscreen);

    updateTheme();
    
    // Initialize based on reader type
    if (readerType === 'pdf') {
        initPdfReader();
    } else if (readerType === 'text' || readerType === 'epub') {
        initTextReader();
    } else {
        updatePageMeta();
    }
}
