<?php
/**
 * Notifications Page — Full-page notification list
 * Shows all notifications with filters, grouping, deep links, and mark-as-read.
 */

$page_title = t('Notifications');
$page = 'notifications';
$user = current_user();

// Load notification functions
if (file_exists(BASE_PATH . '/includes/notification-functions.php')) {
    require_once BASE_PATH . '/includes/notification-functions.php';
}

// Filter: all | action | info
$filter = $_GET['filter'] ?? 'all';
if (!in_array($filter, ['all', 'action', 'info'])) {
    $filter = 'all';
}

// Fetch notifications (server-side, first 50)
$all_notifications = [];
$unread_count = 0;
if (function_exists('notifications_table_exists') && notifications_table_exists()) {
    $result = get_user_notifications((int) $user['id'], 50, 0);
    $all_notifications = $result['notifications'];
    $unread_count = $result['unread_count'];
}

// Apply filter
$notifications = [];
foreach ($all_notifications as $n) {
    $data = $n['data'] ?? [];
    $is_action = is_action_required_notification($n['type'], $data);
    if ($filter === 'action' && !$is_action) continue;
    if ($filter === 'info' && $is_action) continue;
    $notifications[] = $n;
}

// Group by date
$grouped = group_notifications($notifications);

require_once BASE_PATH . '/includes/header.php';
?>

<style>
    .notif-page-wrap {
        max-width: 800px;
        margin: 0 auto;
        padding: 24px 16px;
    }
    .notif-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .notif-page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .notif-page-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        background: #ef4444;
        border-radius: 11px;
    }
    .notif-filter-tabs {
        display: flex;
        gap: 4px;
        padding: 3px;
        border-radius: 10px;
        background: var(--surface-secondary, #f1f5f9);
    }
    .notif-filter-tab {
        padding: 6px 14px;
        font-size: 0.8125rem;
        font-weight: 500;
        border-radius: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .notif-filter-tab:hover {
        background: var(--surface-primary, #fff);
        color: var(--text-primary);
    }
    .notif-filter-tab.active {
        background: var(--surface-primary, #fff);
        color: var(--primary, #3b82f6);
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .notif-group-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        padding: 12px 4px 6px;
    }
    .notif-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        text-decoration: none;
        transition: background 0.12s, border-color 0.12s;
        border-left: 3px solid transparent;
        position: relative;
    }
    .notif-card:hover {
        background: var(--primary-soft, rgba(59,130,246,0.04));
    }
    .notif-card.unread {
        border-left-color: var(--accent-primary, #3b82f6);
        background: var(--primary-soft, rgba(59,130,246,0.04));
    }
    .notif-card .notif-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        overflow: hidden;
    }
    .notif-card .notif-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .notif-card-content {
        flex: 1;
        min-width: 0;
    }
    .notif-card-text {
        font-size: 0.875rem;
        line-height: 1.4;
        color: var(--text-primary);
    }
    .notif-card.unread .notif-card-text {
        font-weight: 600;
    }
    .notif-card-snippet {
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 500px;
    }
    .notif-card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }
    .notif-type-icon {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 0.6875rem;
    }
    .notif-card-time {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .notif-action-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 2px 6px;
        border-radius: 99px;
        background: #fff7ed;
        color: #ea580c;
    }
    [data-theme="dark"] .notif-action-badge {
        background: rgba(234, 88, 12, 0.15);
        color: #fb923c;
    }
    .notif-card-actions {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.15s;
    }
    .notif-card:hover .notif-card-actions {
        opacity: 1;
    }
    .notif-mark-read-btn {
        padding: 4px;
        border-radius: 6px;
        color: var(--text-muted);
        cursor: pointer;
        transition: color 0.12s, background 0.12s;
        border: none;
        background: none;
    }
    .notif-mark-read-btn:hover {
        color: var(--primary, #3b82f6);
        background: var(--primary-soft, rgba(59,130,246,0.08));
    }
    .notif-empty {
        text-align: center;
        padding: 48px 16px;
        color: var(--text-muted);
    }
    .notif-empty-icon {
        opacity: 0.25;
        margin-bottom: 12px;
    }
    .notif-mark-all-btn {
        padding: 6px 14px;
        font-size: 0.8125rem;
        font-weight: 500;
        border-radius: 8px;
        border: 1px solid var(--border-light);
        background: var(--surface-primary, #fff);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
    }
    .notif-mark-all-btn:hover {
        border-color: var(--primary, #3b82f6);
        color: var(--primary, #3b82f6);
    }
    .notif-load-more {
        display: block;
        width: 100%;
        padding: 12px;
        text-align: center;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--primary, #3b82f6);
        background: none;
        border: 1px dashed var(--border-light);
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.15s;
        margin-top: 12px;
    }
    .notif-load-more:hover {
        background: var(--primary-soft, rgba(59,130,246,0.04));
    }
    @media (max-width: 640px) {
        .notif-page-header { flex-direction: column; align-items: flex-start; }
        .notif-card-snippet { max-width: 250px; }
    }
</style>

<div class="notif-page-wrap">
    <!-- Header -->
    <div class="notif-page-header">
        <div class="notif-page-title">
            <?php echo get_icon('bell', 'w-6 h-6'); ?>
            <?php echo e(t('Notifications')); ?>
            <?php if ($unread_count > 0): ?>
                <span class="notif-page-badge"><?php echo $unread_count > 99 ? '99+' : $unread_count; ?></span>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($unread_count > 0): ?>
                <button type="button" class="notif-mark-all-btn" onclick="markAllNotifRead()">
                    <?php echo get_icon('check', 'w-4 h-4 inline-block'); ?>
                    <?php echo e(t('Mark all as read')); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="notif-filter-tabs" style="margin-bottom: 16px;">
        <a href="<?php echo url('notifications'); ?>"
           class="notif-filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
            <?php echo e(t('All')); ?>
        </a>
        <a href="<?php echo url('notifications', ['filter' => 'action']); ?>"
           class="notif-filter-tab <?php echo $filter === 'action' ? 'active' : ''; ?>">
            <?php echo e(t('Action required')); ?>
        </a>
        <a href="<?php echo url('notifications', ['filter' => 'info']); ?>"
           class="notif-filter-tab <?php echo $filter === 'info' ? 'active' : ''; ?>">
            <?php echo e(t('Informational')); ?>
        </a>
    </div>

    <!-- Notification list -->
    <div id="notif-list">
        <?php if (empty($notifications)): ?>
            <div class="notif-empty">
                <?php echo get_icon('bell', 'w-12 h-12 notif-empty-icon'); ?>
                <p class="text-base font-medium" style="color: var(--text-secondary);"><?php echo e(t('No notifications')); ?></p>
                <p class="text-sm mt-1"><?php echo e(t('Activity on your tickets will appear here')); ?></p>
            </div>
        <?php else: ?>
            <?php
            $group_labels = [
                'today' => t('Today'),
                'yesterday' => t('Yesterday'),
                'earlier' => t('Earlier'),
            ];
            foreach (['today', 'yesterday', 'earlier'] as $grp):
                if (empty($grouped[$grp])) continue;
            ?>
                <div class="notif-group-label"><?php echo e($group_labels[$grp]); ?></div>
                <div class="space-y-1">
                    <?php foreach ($grouped[$grp] as $notif):
                        $n_is_read = (bool) $notif['is_read'];
                        $n_text = format_notification_text($notif);
                        $n_time = notification_time_ago($notif['created_at']);
                        $n_ticket_id = $notif['ticket_id'] ? (int) $notif['ticket_id'] : null;
                        $n_data = $notif['data'] ?? [];
                        $n_comment_id = $n_data['comment_id'] ?? null;
                        $n_snippet = get_notification_snippet($notif);
                        $n_is_action = is_action_required_notification($notif['type'], $n_data);

                        // Build deep link URL (ref + nid for back nav & auto mark-read)
                        $n_href = '#';
                        if ($n_ticket_id) {
                            $n_href = 'index.php?page=ticket&id=' . $n_ticket_id . '&ref=notifications&nid=' . (int)$notif['id'];
                            if ($n_comment_id) {
                                $n_href .= '#comment-' . $n_comment_id;
                            }
                        }

                        $n_actor_name = trim(($notif['actor_first_name'] ?? '') . ' ' . ($notif['actor_last_name'] ?? ''));
                        $n_actor_avatar = $notif['actor_avatar'] ?? null;
                        $n_initials = mb_strtoupper(mb_substr($notif['actor_first_name'] ?? '?', 0, 1));

                        // Avatar color (crc32 avoids float overflow from bitshift)
                        $avatar_bg = 'hsl(' . abs(crc32($n_actor_name)) % 360 . ', 55%, 60%)';

                        // Type icon + color
                        $type_icon = 'bell';
                        $type_color = '#6b7280';
                        switch ($notif['type']) {
                            case 'new_ticket':      $type_icon = 'plus';                 $type_color = '#10b981'; break;
                            case 'new_comment':     $type_icon = 'comment';              $type_color = '#3b82f6'; break;
                            case 'status_changed':  $type_icon = 'refresh-cw';           $type_color = '#8b5cf6'; break;
                            case 'assigned_to_you': $type_icon = 'user-plus';            $type_color = '#f59e0b'; break;
                            case 'priority_changed': $type_icon = 'exclamation-triangle'; $type_color = '#ef4444'; break;
                            case 'ticket_updated':  $type_icon = 'edit';                 $type_color = '#6366f1'; break;
                            case 'due_date_reminder': $type_icon = 'clock';              $type_color = '#ef4444'; break;
                        }
                    ?>
                        <div class="notif-card <?php echo $n_is_read ? '' : 'unread'; ?>"
                             id="notif-item-<?php echo (int)$notif['id']; ?>"
                             data-id="<?php echo (int)$notif['id']; ?>">
                            <!-- Avatar -->
                            <a href="<?php echo $n_href; ?>" class="notif-avatar" style="background: <?php echo $avatar_bg; ?>;">
                                <?php if ($n_actor_avatar && !str_starts_with($n_actor_avatar, 'data:')): ?>
                                    <img src="<?php echo e(upload_url($n_actor_avatar)); ?>" alt=""
                                         onerror="this.style.display='none';this.parentElement.textContent='<?php echo e($n_initials); ?>'">
                                <?php elseif ($n_actor_avatar && str_starts_with($n_actor_avatar, 'data:')): ?>
                                    <img src="<?php echo e($n_actor_avatar); ?>" alt="">
                                <?php else: ?>
                                    <?php echo e($n_initials); ?>
                                <?php endif; ?>
                            </a>
                            <!-- Content -->
                            <a href="<?php echo $n_href; ?>" class="notif-card-content" style="text-decoration: none;">
                                <div class="notif-card-text"><?php echo e($n_text); ?></div>
                                <?php if ($n_snippet): ?>
                                    <div class="notif-card-snippet"><?php echo e($n_snippet); ?></div>
                                <?php endif; ?>
                                <div class="notif-card-meta">
                                    <span class="notif-type-icon" style="color: <?php echo e($type_color); ?>;">
                                        <?php echo get_icon($type_icon, 'w-3 h-3'); ?>
                                    </span>
                                    <span class="notif-card-time"><?php echo e($n_time); ?></span>
                                    <?php if ($n_is_action): ?>
                                        <span class="notif-action-badge"><?php echo e(t('Action required')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <!-- Actions -->
                            <div class="notif-card-actions">
                                <?php if (!$n_is_read): ?>
                                    <button type="button" class="notif-mark-read-btn"
                                            onclick="event.stopPropagation(); markNotifRead(<?php echo (int)$notif['id']; ?>)"
                                            title="<?php echo e(t('Mark as read')); ?>">
                                        <?php echo get_icon('check', 'w-4 h-4'); ?>
                                    </button>
                                <?php endif; ?>
                                <a href="<?php echo $n_href; ?>" class="notif-mark-read-btn"
                                   title="<?php echo e(t('Open')); ?>">
                                    <?php echo get_icon('chevron-right', 'w-4 h-4'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <?php if (count($all_notifications) >= 50): ?>
                <button type="button" class="notif-load-more" id="notif-load-more-btn"
                        onclick="loadMoreNotifs()">
                    <?php echo e(t('Load more')); ?>
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    var _offset = <?php echo count($all_notifications); ?>;
    var _filter = <?php echo json_encode($filter); ?>;
    var _loading = false;

    window.markNotifRead = function(id) {
        fetch('index.php?page=api&action=mark-notification-read', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'notification_id=' + id + '&csrf_token=' + encodeURIComponent(window.csrfToken)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                var el = document.getElementById('notif-item-' + id);
                if (el) {
                    el.classList.remove('unread');
                    var btn = el.querySelector('.notif-mark-read-btn');
                    if (btn && btn.tagName === 'BUTTON') btn.remove();
                }
                // Update badges
                if (typeof updateBadge === 'function') {
                    var badge = document.querySelector('.notif-page-badge');
                    if (badge) {
                        var c = parseInt(badge.textContent) - 1;
                        if (c <= 0) badge.remove();
                        else badge.textContent = c > 99 ? '99+' : c;
                    }
                }
            }
        });
    };

    window.markAllNotifRead = function() {
        fetch('index.php?page=api&action=mark-all-notifications-read', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'csrf_token=' + encodeURIComponent(window.csrfToken)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                document.querySelectorAll('.notif-card.unread').forEach(function(el) {
                    el.classList.remove('unread');
                });
                document.querySelectorAll('.notif-mark-read-btn').forEach(function(btn) {
                    if (btn.tagName === 'BUTTON') btn.remove();
                });
                var badge = document.querySelector('.notif-page-badge');
                if (badge) badge.remove();
                var markAllBtn = document.querySelector('.notif-mark-all-btn');
                if (markAllBtn) markAllBtn.remove();
                if (typeof updateBadge === 'function') updateBadge(0);
            }
        });
    };

    window.loadMoreNotifs = function() {
        if (_loading) return;
        _loading = true;
        var btn = document.getElementById('notif-load-more-btn');
        if (btn) btn.textContent = '...';

        fetch('index.php?page=api&action=get-notifications&limit=50&offset=' + _offset)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            _loading = false;
            if (!res.success) return;

            var groups = res.groups || {};
            var hasItems = false;
            var container = document.getElementById('notif-list');

            ['today', 'yesterday', 'earlier'].forEach(function(grp) {
                var items = groups[grp] || [];
                items.forEach(function(n) {
                    // Apply filter
                    var isAction = (n.type === 'assigned_to_you' || n.type === 'due_date_reminder' ||
                                   (n.type === 'new_comment' && n.data && n.data.action_required));
                    if (_filter === 'action' && !isAction) return;
                    if (_filter === 'info' && isAction) return;

                    hasItems = true;
                    _offset++;
                    var card = buildNotifCard(n);
                    // Find or create group container
                    var lastDiv = container.querySelector('.space-y-1:last-of-type');
                    if (lastDiv) lastDiv.insertAdjacentHTML('beforeend', card);
                    else container.insertAdjacentHTML('beforeend', '<div class="space-y-1">' + card + '</div>');
                });
            });

            if (!hasItems || _offset >= 200) {
                if (btn) btn.remove();
            } else {
                if (btn) btn.textContent = <?php echo json_encode(t('Load more')); ?>;
            }
        })
        .catch(function() {
            _loading = false;
            if (btn) btn.textContent = <?php echo json_encode(t('Load more')); ?>;
        });
    };

    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    function avatarColor(name) {
        var h = 0;
        for (var i = 0; i < name.length; i++) h = (name.charCodeAt(i) + ((h << 5) - h));
        return 'hsl(' + (Math.abs(h) % 360) + ', 55%, 60%)';
    }

    function buildNotifCard(n) {
        var data = n.data || {};
        var isRead = !!n.is_read;
        var isAction = (n.type === 'assigned_to_you' || n.type === 'due_date_reminder' ||
                       (n.type === 'new_comment' && data.action_required));
        var actorName = (n.actor_first_name || '') + ' ' + (n.actor_last_name || '');
        actorName = actorName.trim();
        var initials = (n.actor_first_name || '?').charAt(0).toUpperCase();
        var snippet = data.comment_preview || '';
        var commentId = data.comment_id || null;
        var ticketId = n.ticket_id || null;
        var href = '#';
        if (ticketId) {
            href = 'index.php?page=ticket&id=' + ticketId + '&ref=notifications&nid=' + n.id;
            if (commentId) href += '#comment-' + commentId;
        }

        var typeIcon = 'bell', typeColor = '#6b7280';
        switch(n.type) {
            case 'new_ticket': typeIcon = 'plus'; typeColor = '#10b981'; break;
            case 'new_comment': typeIcon = 'comment'; typeColor = '#3b82f6'; break;
            case 'status_changed': typeIcon = 'refresh-cw'; typeColor = '#8b5cf6'; break;
            case 'assigned_to_you': typeIcon = 'user-plus'; typeColor = '#f59e0b'; break;
            case 'priority_changed': typeIcon = 'exclamation-triangle'; typeColor = '#ef4444'; break;
            case 'ticket_updated': typeIcon = 'edit'; typeColor = '#6366f1'; break;
            case 'due_date_reminder': typeIcon = 'clock'; typeColor = '#ef4444'; break;
        }

        // Use formatted text from backend if available, otherwise fallback
        var text = n.formatted_text || esc(data.actor_name || actorName) + ' — ' + esc(n.type);

        var html = '<div class="notif-card ' + (isRead ? '' : 'unread') + '" id="notif-item-' + n.id + '" data-id="' + n.id + '">';
        html += '<a href="' + esc(href) + '" class="notif-avatar" style="background:' + avatarColor(actorName) + '">';
        if (n.actor_avatar) {
            html += '<img src="' + esc(n.actor_avatar) + '" alt="" onerror="this.style.display=\'none\';this.parentElement.textContent=\'' + esc(initials) + '\'">';
        } else {
            html += esc(initials);
        }
        html += '</a>';
        html += '<a href="' + esc(href) + '" class="notif-card-content" style="text-decoration:none">';
        html += '<div class="notif-card-text">' + esc(text) + '</div>';
        if (snippet) html += '<div class="notif-card-snippet">' + esc(snippet) + '</div>';
        html += '<div class="notif-card-meta">';
        html += '<span class="notif-card-time">' + esc(n.time_ago || '') + '</span>';
        if (isAction) html += '<span class="notif-action-badge">' + esc(<?php echo json_encode(t('Action required')); ?>) + '</span>';
        html += '</div></a>';
        html += '<div class="notif-card-actions">';
        if (!isRead) {
            html += '<button type="button" class="notif-mark-read-btn" onclick="event.stopPropagation();markNotifRead(' + n.id + ')" title="' + esc(<?php echo json_encode(t('Mark as read')); ?>) + '">';
            html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            html += '</button>';
        }
        html += '<a href="' + esc(href) + '" class="notif-mark-read-btn">';
        html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
        html += '</a></div></div>';
        return html;
    }
})();
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
