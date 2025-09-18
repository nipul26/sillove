define([
    'jquery'
], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {

            // Quantity increment
            $(document).on('click', '.increaseQty', function () {
                var $qty = $('#qty');
                var currentVal = parseInt($qty.val()) || 0;
                $qty.val(currentVal + 1).trigger('change');
            });

            // Quantity decrement
            $(document).on('click', '.decreaseQty', function () {
                var $qty = $('#qty');
                var currentVal = parseInt($qty.val()) || 0;
                if (currentVal > 1) {
                    $qty.val(currentVal - 1).trigger('change');
                }
            });

            // Extra: prevent qty < 1 manually typed
            $(document).on('change', '#qty', function () {
                var $qty = $(this);
                if (parseInt($qty.val()) < 1 || isNaN($qty.val())) {
                    $qty.val(1);
                }
            });

        });
    };
});
