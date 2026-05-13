/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });

    $('.header.content > .header.links').clone().appendTo('#store\\.links');
    $('#store\\.links li a').each(function () {
        var id = $(this).attr('id');

        if (id !== undefined) {
            $(this).attr('id', id + '_mobile');
        }
    });

    var miniCart = $('[data-block="minicart"]');

    miniCart.on('dropdowndialogopen', function () {
        $('body').addClass('minicart-popup-active');
    });

    miniCart.on('dropdowndialogclose', function () {
        $('body').removeClass('minicart-popup-active');
    });

    keyboardHandler.apply();
});
