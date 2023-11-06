<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    (function () {
        let root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        let post = {
            'toggle': $('[data-role="post.toggle"]', root),
            'content': $('[data-role="post.content"]', root)
        };

        if (post.content.length) {
            let actions = {};

            actions.hide = function () {
                post.toggle.css('pointer-events', 'none');
                post.content.css('height', post.content.outerHeight());

                setTimeout(function () {
                    post.content.animate({
                       'height': 0
                    }, 401, function () {
                        post.toggle.css('pointer-events', '');
                        post.content
                            .attr('data-expanded', 'false')
                            .css('height', '');
                    });
                }, 1);
            };

            actions.show = function () {
                let height = null;

                post.toggle.css('pointer-events', 'none');
                post.content.attr('data-expanded', 'true');

                height = post.content.outerHeight();
                post.content.css('height', 0);

                setTimeout(function () {
                    post.content.animate({
                        'height': height
                    }, 401, function () {
                        post.toggle.css('pointer-events', '');
                        post.content.css('height', '');
                    });
                }, 1);
            };

            post.toggle.on('click', function () {
                let expanded = post.content.attr('data-expanded') === 'true';

                if (expanded)
                    actions.hide();
                else
                    actions.show();
            });
        }
    })();
</script>