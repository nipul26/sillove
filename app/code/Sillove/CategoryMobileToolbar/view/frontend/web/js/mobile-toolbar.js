/**
 * Mobile bottom toolbar: Sort By and Filter actions for category/search pages.
 */
define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    var MOBILE_MAX_WIDTH = 767;

    return function (config, element) {
        var $toolbar = $(element),
            $sortPanel = $('.bv-mobile-sort'),
            $sortList = $sortPanel.find('.bv-mobile-sort__list'),
            $filterHeader = $('.bv-mobile-filter-header'),
            $filterBtn = $toolbar.find('.bv-mobile-toolbar__btn--filter'),
            $sortBtn = $toolbar.find('.bv-mobile-toolbar__btn--sort'),
            $sorter = $('#sorter'),
            $filterTitle = $('.filter-title strong[data-role="title"]'),
            mediaQuery = window.matchMedia('(max-width: ' + MOBILE_MAX_WIDTH + 'px)');

        function isMobile() {
            return mediaQuery.matches;
        }

        function getFilterCount() {
            var count = $filterTitle.closest('.filter-title').attr('data-count');

            return count ? parseInt(count, 10) : 0;
        }

        function updateFilterBadges() {
            var count = getFilterCount(),
                $toolbarBadge = $filterBtn.find('.bv-mobile-toolbar__count'),
                $headerBadge = $filterHeader.find('.bv-mobile-filter-header__badge');

            if (count > 0) {
                $toolbarBadge.text(count).show();
                $headerBadge.text(count).show();
            } else {
                $toolbarBadge.hide();
                $headerBadge.hide();
            }
        }

        function buildSortOptions() {
            $sortList.empty();

            if (!$sorter.length) {
                return false;
            }

            $sorter.find('option').each(function () {
                var $option = $(this),
                    value = $option.val(),
                    label = $option.text(),
                    isSelected = $option.is(':selected'),
                    $btn = $('<button/>', {
                        type: 'button',
                        class: 'bv-mobile-sort__option' + (isSelected ? ' is-selected' : ''),
                        'data-value': value,
                        text: label,
                        role: 'option',
                        'aria-selected': isSelected ? 'true' : 'false'
                    });

                $sortList.append($('<li/>').append($btn));
            });

            return $sortList.children().length > 0;
        }

        function toggleToolbar() {
            var hasSorter = $sorter.length && $sorter.find('option').length > 0,
                hasFilter = $filterTitle.length && !$('#layered-filter-block').hasClass('filter-no-options');

            if (!isMobile() || (!hasSorter && !hasFilter)) {
                $toolbar.hide();
                $('body').removeClass('bv-mobile-toolbar-active');
                return;
            }

            $toolbar.show();
            $('body').addClass('bv-mobile-toolbar-active');

            $sortBtn.toggle(!!hasSorter);
            $filterBtn.toggle(!!hasFilter);

            if (hasFilter) {
                updateFilterBadges();
            }

            $toolbar
                .toggleClass('bv-mobile-toolbar--dual', hasSorter && hasFilter)
                .toggleClass('bv-mobile-toolbar--sort-only', hasSorter && !hasFilter)
                .toggleClass('bv-mobile-toolbar--filter-only', !hasSorter && hasFilter);
        }

        function openSortPanel() {
            if (!buildSortOptions()) {
                return;
            }

            $sortPanel.show().attr('aria-hidden', 'false');
            $('body').addClass('bv-sort-active');
        }

        function closeSortPanel() {
            $sortPanel.hide().attr('aria-hidden', 'true');
            $('body').removeClass('bv-sort-active');
        }

        function openFilter() {
            if ($filterTitle.length) {
                $filterTitle.trigger('click');
            }
        }

        function closeFilter() {
            if ($('body').hasClass('filter-active') && $filterTitle.length) {
                $filterTitle.trigger('click');
            }
        }

        function showFilterHeader() {
            updateFilterBadges();
            $filterHeader.show().attr('aria-hidden', 'false');
        }

        function hideFilterHeader() {
            $filterHeader.hide().attr('aria-hidden', 'true');
        }

        function watchFilterState() {
            var observer = new MutationObserver(function () {
                if ($('body').hasClass('filter-active') && isMobile()) {
                    showFilterHeader();
                } else {
                    hideFilterHeader();
                }
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        $sortBtn.on('click', function () {
            openSortPanel();
        });

        $filterBtn.on('click', function () {
            openFilter();
        });

        $sortPanel.find('.bv-mobile-sort__close').on('click', function () {
            closeSortPanel();
        });

        $filterHeader.find('.bv-mobile-filter-header__close').on('click', function () {
            closeFilter();
        });

        $sortList.on('click', '.bv-mobile-sort__option', function () {
            var value = $(this).data('value');

            $sorter.val(value).trigger('change');
            closeSortPanel();
        });

        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', toggleToolbar);
        } else {
            mediaQuery.addListener(toggleToolbar);
        }

        watchFilterState();
        toggleToolbar();
    };
});
