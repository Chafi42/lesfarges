(function ($) {
    $(function () {
        $(document).on('click', '.wpdi-button', function() {
            var $this = $(this);
            var $parent = $(this).closest('p');
            $parent.html('Running...');
            $.post(dependencies.ajaxurl, {
                action: 'dependency_installer',
                method: $this.attr('data-action'),
                slug: $this.attr('data-slug'),
                nonce: $this.attr('data-nonce')
            }, function(response) {
                $parent.html(response);
            });
        });
        $(document).on('click', '.dependency-installer .notice-dismiss', function() {
            var $this = $(this);
            $.post(dependencies.ajaxurl, {
                action: 'dependency_installer',
                method: 'dismiss',
                slug: $this.attr('data-slug')
            });
        });
    });
})(jQuery);
