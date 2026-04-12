define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magecomp_Discountpercentage/checkout/summary/custom-text'
        },

        totals: quote.getTotals(),

        /**
         * @return {Boolean}
         */
        isEnabled: function () {
            return !!(window.checkoutConfig.magecomp_custom_text &&
                window.checkoutConfig.magecomp_custom_text.enabled);
        },

        /**
         * @return {String}
         */
        getLabel: function () {
            if (window.checkoutConfig.magecomp_custom_text) {
                return window.checkoutConfig.magecomp_custom_text.label || '';
            }
            return '';
        },

        /**
         * @return {String}
         */
        getCustomText: function () {
            if (window.checkoutConfig.magecomp_custom_text) {
                return window.checkoutConfig.magecomp_custom_text.text || '';
            }
            return '';
        },

        /**
         * @return {Boolean}
         */
        isDisplayed: function () {
            return this.isEnabled() && this.isFullMode();
        }
    });
});
