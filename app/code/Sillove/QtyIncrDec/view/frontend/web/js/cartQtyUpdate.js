define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data',
    'mage/loader'
], function ($, getTotalsAction, customerData) {
    'use strict';

    $(document).ready(function () {

        // On qty input change
        $(document).on('change', 'input[name$="[qty]"]', function () {
            var form = $('form#form-validate');
            $('body').loader('show');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'html',
                success: function (res) {
                    var parsedResponse = $.parseHTML(res);
                    var result = $(parsedResponse).find("#form-validate");
                    var sections = ['cart'];

                    $("#form-validate").replaceWith(result);

                    // Reload minicart
                    customerData.reload(sections, true);

                    // Reload totals
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                },
                complete: function () {
                    $('body').loader('hide');
                },
                error: function (xhr, status, error) {
                    console.error("Cart update error:", error);
                }
            });
        });

        // On increase/decrease button click
        $(document).on('click', '.increaseQty, .decreaseQty', function (e) {
            e.preventDefault();

            var $btn = $(this);
            var $input = $btn.closest('tr').find('input[name$="[qty]"]');
            var currentVal = parseInt($input.val()) || 1;

            if ($btn.hasClass('increaseQty')) {
                $input.val(currentVal + 1);
            } else if ($btn.hasClass('decreaseQty') && currentVal > 1) {
                $input.val(currentVal - 1);
            }

            $input.trigger('change');

            var form = $('form#form-validate');
            $('body').loader('show');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'html',
                success: function (res) {
                    var parsedResponse = $.parseHTML(res);
                    var result = $(parsedResponse).find("#form-validate");
                    var sections = ['cart'];

                    $("#form-validate").replaceWith(result);
                    customerData.reload(sections, true);

                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                },
                complete: function () {
                    $('body').loader('hide');
                },
                error: function (xhr, status, error) {
                    console.error("Cart update error:", error);
                }
            });
        });
    });
});
