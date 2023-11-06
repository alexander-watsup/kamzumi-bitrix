(function ($) {
    $(function () {
        var area;
        var handler;
        var template;
        var body;
        var isEditor;
        var loader;
        var interval;

        loader = function () {
            body = $('body');
            template = $('.intec-template');
            isEditor = template.data('editor');

            if (template.length > 0) {
                area = $(window);
                handler = function () {
                    template.attr('data-adaptive', 'false');

                    if (!isEditor)
                        body.css('height', '');

                    if (template.outerHeight() < area.height()) {
                        if (!isEditor)
                            body.css('height', '100%');

                        template.attr('data-adaptive', 'true');
                    }
                };

                area.on('resize', handler);

                if (isEditor)
                    area = template.parent();

                window.setInterval(handler, 250);
                handler();

                return true;
            }

            return false;
        };

        interval = window.setInterval(function () {
            if (loader())
                window.clearInterval(interval);
        }, 250);
    });
})(jQuery);
