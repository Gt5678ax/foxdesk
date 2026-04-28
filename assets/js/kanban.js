/**
 * Kanban Board — Drag & Drop + Mobile Fallback
 * Uses HTML5 Drag and Drop API, calls existing change-status API endpoint.
 */
(function () {
    var board = document.querySelector('.kanban-board-wrapper');
    if (!board) return;
    var cfg = window.appConfig || {};

    var draggedCard = null;
    var sourceColumn = null;
    var placeholder = null;

    // Create drop placeholder element
    function createPlaceholder() {
        var el = document.createElement('div');
        el.className = 'kanban-drop-placeholder';
        return el;
    }

    // --- Drag and Drop ---

    var dragGhost = null;

    board.addEventListener('dragstart', function (e) {
        var card = e.target.closest('.kanban-card');
        if (!card || !card.hasAttribute('draggable')) return;
        draggedCard = card;
        sourceColumn = card.closest('.kanban-cards');
        placeholder = createPlaceholder();
        placeholder.style.height = card.offsetHeight + 'px';

        // Build a custom drag ghost (Trello-style thumbnail)
        dragGhost = card.cloneNode(true);
        dragGhost.classList.add('kanban-drag-ghost');
        // Remove mobile select from ghost
        var sel = dragGhost.querySelector('.kanban-mobile-status');
        if (sel) sel.remove();
        var srcCol = card.closest('.kanban-column');
        // Size it to match the card
        dragGhost.style.width = card.offsetWidth + 'px';
        document.body.appendChild(dragGhost);
        e.dataTransfer.setDragImage(dragGhost, card.offsetWidth / 2, 20);

        // Slight delay so browser captures the drag image before we style it
        requestAnimationFrame(function () {
            card.classList.add('dragging');
            if (srcCol) srcCol.classList.add('drag-source');
        });

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', card.dataset.ticketId);
    });

    board.addEventListener('dragover', function (e) {
        if (!draggedCard) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        var col = e.target.closest('.kanban-column');
        if (!col) return;

        // Highlight target column
        board.querySelectorAll('.kanban-column.drag-over').forEach(function (c) {
            if (c !== col) {
                c.classList.remove('drag-over');
                removePlaceholder(c);
            }
        });
        col.classList.add('drag-over');

        // Position placeholder among cards
        var cardsContainer = col.querySelector('.kanban-cards');
        if (!cardsContainer) return;

        var afterCard = getCardAfterCursor(cardsContainer, e.clientY);
        if (afterCard) {
            cardsContainer.insertBefore(placeholder, afterCard);
        } else {
            cardsContainer.appendChild(placeholder);
        }
    });

    board.addEventListener('dragleave', function (e) {
        var col = e.target.closest('.kanban-column');
        if (col && !col.contains(e.relatedTarget)) {
            col.classList.remove('drag-over');
            removePlaceholder(col);
        }
    });

    board.addEventListener('drop', function (e) {
        e.preventDefault();
        var col = e.target.closest('.kanban-column');
        if (!col || !draggedCard) return;
        col.classList.remove('drag-over');

        var newStatusId = col.dataset.statusId;
        var ticketId = draggedCard.dataset.ticketId;
        var oldStatusId = draggedCard.dataset.statusId;
        var oldScope = draggedCard.dataset.kanbanScope || 'main';
        var targetIsClosed = col.dataset.isClosed === '1';
        var newScope = targetIsClosed && oldScope === 'archived' ? 'archived' : 'main';

        // Insert card where placeholder is
        var targetCards = findCardsContainer(newStatusId, newScope) || col.querySelector('.kanban-cards');
        var savedSource = sourceColumn;

        if (placeholder && placeholder.parentNode === targetCards) {
            targetCards.insertBefore(draggedCard, placeholder);
        } else {
            targetCards.appendChild(draggedCard);
        }

        removePlaceholderGlobal();

        if (newStatusId === oldStatusId) {
            if (newScope !== oldScope && savedSource) {
                savedSource.appendChild(draggedCard);
            }
            cleanup();
            return;
        }

        // Animate the landed card
        draggedCard.classList.remove('dragging');
        draggedCard.classList.add('just-dropped');
        var droppedCard = draggedCard;
        setTimeout(function () { droppedCard.classList.remove('just-dropped'); }, 500);

        draggedCard.dataset.statusId = newStatusId;
        draggedCard.dataset.kanbanScope = newScope;
        updateMobileSelect(draggedCard, newStatusId);
        cleanup();
        animateColumnCounts();

        // API call
        changeStatus(ticketId, newStatusId, function onError() {
            // Revert with shake animation
            var card = document.querySelector('.kanban-card[data-ticket-id="' + ticketId + '"]');
            if (card) {
                card.classList.add('revert-shake');
                setTimeout(function () { card.classList.remove('revert-shake'); }, 400);
            }
            var sourceTarget = savedSource || findCardsContainer(oldStatusId, oldScope);
            var revertCard = card || document.querySelector('.kanban-card[data-ticket-id="' + ticketId + '"]');
            if (sourceTarget && revertCard) {
                sourceTarget.appendChild(revertCard);
            }
            if (card) {
                card.dataset.statusId = oldStatusId;
                card.dataset.kanbanScope = oldScope;
                updateMobileSelect(card, oldStatusId);
            }
            animateColumnCounts();
        });
    });

    board.addEventListener('dragend', function () {
        cleanup();
        removePlaceholderGlobal();
        board.querySelectorAll('.drag-over, .drag-source').forEach(function (el) {
            el.classList.remove('drag-over', 'drag-source');
        });
    });

    // --- Determine insertion point based on cursor Y position ---

    function getCardAfterCursor(container, y) {
        var cards = Array.from(container.querySelectorAll('.kanban-card:not(.dragging)'));
        var closest = null;
        var closestOffset = Number.NEGATIVE_INFINITY;

        cards.forEach(function (card) {
            var box = card.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closestOffset) {
                closestOffset = offset;
                closest = card;
            }
        });

        return closest;
    }

    // --- Mobile fallback: status select ---

    board.addEventListener('change', function (e) {
        var sel = e.target.closest('.kanban-mobile-status');
        if (!sel) return;

        var ticketId = sel.dataset.ticketId;
        var newStatusId = sel.value;
        var card = sel.closest('.kanban-card');
        var oldStatusId = card.dataset.statusId;
        var oldScope = card.dataset.kanbanScope || 'main';
        var selectedOption = sel.options[sel.selectedIndex];
        var targetIsClosed = !!selectedOption && selectedOption.dataset.isClosed === '1';
        var targetScope = targetIsClosed && oldScope === 'archived' ? 'archived' : 'main';

        if (newStatusId === oldStatusId) return;

        // Animate card out
        card.classList.add('card-fly-out');
        setTimeout(function () {
            card.classList.remove('card-fly-out');

            // Move card to new column
            var targetCol = findCardsContainer(newStatusId, targetScope);
            if (targetCol) {
                targetCol.appendChild(card);
                card.dataset.statusId = newStatusId;
                card.dataset.kanbanScope = targetScope;
                card.classList.add('card-fly-in');
                setTimeout(function () { card.classList.remove('card-fly-in'); }, 350);
                animateColumnCounts();
            }

            changeStatus(ticketId, newStatusId, function onError() {
                var sourceCol = findCardsContainer(oldStatusId, oldScope);
                if (sourceCol) {
                    sourceCol.appendChild(card);
                    card.dataset.statusId = oldStatusId;
                    card.dataset.kanbanScope = oldScope;
                    sel.value = oldStatusId;
                    card.classList.add('revert-shake');
                    setTimeout(function () { card.classList.remove('revert-shake'); }, 400);
                    animateColumnCounts();
                }
            });
        }, 200);
    });

    // --- Helpers ---

    function cleanup() {
        if (draggedCard) draggedCard.classList.remove('dragging');
        board.querySelectorAll('.drag-source').forEach(function (el) {
            el.classList.remove('drag-source');
        });
        // Remove drag ghost from DOM
        if (dragGhost && dragGhost.parentNode) {
            dragGhost.parentNode.removeChild(dragGhost);
        }
        dragGhost = null;
        draggedCard = null;
        sourceColumn = null;
    }

    function removePlaceholder(col) {
        var ph = col.querySelector('.kanban-drop-placeholder');
        if (ph) ph.remove();
    }

    function removePlaceholderGlobal() {
        board.querySelectorAll('.kanban-drop-placeholder').forEach(function (ph) { ph.remove(); });
        placeholder = null;
    }

    function findCardsContainer(statusId, preferredScope) {
        var selector = '.kanban-cards[data-status-id="' + statusId + '"]';
        if (preferredScope) {
            var scoped = board.querySelector(selector + '[data-kanban-scope="' + preferredScope + '"]');
            if (scoped) return scoped;
        }
        return board.querySelector(selector + '[data-kanban-scope="main"]') || board.querySelector(selector);
    }

    function animateColumnCounts() {
        board.querySelectorAll('.kanban-column').forEach(function (col) {
            var count = col.querySelectorAll('.kanban-card').length;
            var badge = col.querySelector('.kanban-count');
            if (badge) {
                var oldCount = parseInt(badge.textContent, 10);
                if (oldCount !== count) {
                    badge.textContent = count;
                    badge.classList.add('count-pop');
                    setTimeout(function () { badge.classList.remove('count-pop'); }, 300);
                }
            }
        });

        var closedSummary = document.getElementById('kanban-closed-count');
        if (closedSummary) {
            var closedCount = 0;
            board.querySelectorAll('.kanban-column[data-is-closed="1"][data-kanban-scope="archived"]').forEach(function (col) {
                closedCount += col.querySelectorAll('.kanban-card').length;
            });
            closedSummary.textContent = closedCount;
        }
    }

    function updateMobileSelect(card, statusId) {
        var sel = card.querySelector('.kanban-mobile-status');
        if (sel) sel.value = statusId;
    }

    function changeStatus(ticketId, statusId, onError) {
        var body = new URLSearchParams();
        body.append('ticket_id', ticketId);
        body.append('status_id', statusId);

        fetch(window.appConfig.apiUrl + '&action=change-status', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': window.csrfToken,
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (!res.success) {
                if (window.showAppToast) window.showAppToast(res.error || cfg.errorLabel || 'Error', 'error');
                if (onError) onError();
            } else {
                if (window.showAppToast) window.showAppToast(res.message || cfg.savedLabel || 'Saved', 'success');
            }
        })
        .catch(function () {
            if (window.showAppToast) window.showAppToast(cfg.errorLabel || 'Error', 'error');
            if (onError) onError();
        });
    }
})();
