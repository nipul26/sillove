define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/translate'
], function($, Component, ko, $t) {

    return Component.extend({

        initialize: function() {

            var interval;
            interval = setInterval(function() {
                if (!window.couponlistAdded) {
                    if (window.checkoutConfig.couponlist.listCount && window.checkoutConfig.couponlist.moduleStatus) {
                        if ($("#discount-form").length) {
                            window.couponlistAdded = true;
                            $("#discount-form").after($t("<a id='openModel' class='coupon-list-link'>Show Coupon List</a>"));
                            clearInterval(interval);
                        }
                    } else {
                        window.couponlistAdded = true;
                        clearInterval(interval);
                    }
                }
            }, 500);

            self = this;
            this._super();
        },
    });

});