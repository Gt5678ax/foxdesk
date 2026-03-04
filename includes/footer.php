</div>

<!-- Footer -->
<footer class="px-4 lg:px-8 py-3 text-xs" style="color: var(--text-muted); margin-top: auto;">
    <div class="copyright">
        <a href="https://foxdesk.org" target="_blank" rel="noopener" style="color: var(--text-muted);">FoxDesk</a>
    </div>
</footer>
</main>

<script>
    // App config for external JS (bridge PHP → JS)
    window.appConfig = {
        apiUrl: <?php echo json_encode(url('api')); ?>,
        deleteConfirmMsg: <?php echo json_encode(t('Are you sure you want to delete this item?')); ?>,
        invalidFileTypeMsg: <?php echo json_encode(t('Invalid file type.')); ?>,
        isStaff: <?php echo (is_agent() || is_admin()) ? 'true' : 'false'; ?>,
        isAdmin: <?php echo is_admin() ? 'true' : 'false'; ?>,
        pausedLabel: <?php echo json_encode(t('Paused')); ?>,
        activeTimersLabel: <?php echo json_encode(t('Active Timers')); ?>,
        cancelTicketConfirm: <?php echo json_encode(t('Cancel ticket? The ticket will be deleted.')); ?>,
        cancelTicketTooltip: <?php echo json_encode(t('Cancel ticket')); ?>
    };
</script>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(function() {});
}
</script>
<!-- Image Preview Lightbox -->
<div id="image-lightbox" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 backdrop-blur-sm"
     onclick="if(event.target===this)closeImagePreview();">
    <div class="relative max-w-[90vw] max-h-[90vh]">
        <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl">
        <div id="lightbox-name" class="text-center text-white text-sm mt-2 truncate"></div>
        <button onclick="closeImagePreview();" class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black/80 transition text-lg">&times;</button>
    </div>
</div>
<script>
function openImagePreview(src, name) {
    var lb = document.getElementById('image-lightbox');
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-name').textContent = name || '';
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.addEventListener('keydown', _lbEsc);
}
function closeImagePreview() {
    var lb = document.getElementById('image-lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.getElementById('lightbox-img').src = '';
    document.removeEventListener('keydown', _lbEsc);
}
function _lbEsc(e) { if (e.key === 'Escape') closeImagePreview(); }
</script>
<script defer src="assets/js/app-footer.js?v=<?php echo defined('APP_VERSION') ? APP_VERSION : '1'; ?>"></script>
</body>

</html>

