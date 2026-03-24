/**
 * Copyright © 2017 Ecomteck. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';

        return function (target) {
            if (typeof(window.checkoutConfig.onestepcheckout) == 'undefined') {
                return target.extend({});
            }
            return target.extend({
                defaults: {
                    template: 'Ecomteck_OneStepCheckout/payment',
                    activeMethod: ''
                },

                isVisible: ko.observable(true)
            });
        };
    }
);