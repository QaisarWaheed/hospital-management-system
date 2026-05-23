<script>
(function () {
    var page = window.location.pathname.split('/').pop().split('?')[0].toLowerCase();
    if (!page) {
        return;
    }
    document.querySelectorAll('#nav_1 a[href]').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0) {
            return;
        }
        var target = href.split('/').pop().split('?')[0].toLowerCase();
        if (target && target === page) {
            link.classList.add('active');
            if (link.parentElement && link.parentElement.tagName === 'LI') {
                link.parentElement.classList.add('active');
            }
        }
    });
})();
</script>
