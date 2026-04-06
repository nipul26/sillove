define(['jquery', 'domReady!'], function ($) {
    'use strict';

    return function () {
        var currentPath = window.location.pathname.replace(/\/$/, '') || '/';

        $('li.level0.ui-menu-item > a.level-top').each(function () {
            var href = $(this).attr('href');

            if (!href) {
                return;
            }

            var linkParser = document.createElement('a');
            linkParser.href = href;

            var linkPath = linkParser.pathname.replace(/\/$/, '') || '/';

            if (linkPath === currentPath) {
                var $li = $(this).closest('li.level0.ui-menu-item');
                $li.addClass('active');

                // Keep active class persistent
                var observer = new MutationObserver(function () {
                    if (!$li.hasClass('active')) {
                        $li.addClass('active');
                    }
                });

                observer.observe($li[0], {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        });
    };
});