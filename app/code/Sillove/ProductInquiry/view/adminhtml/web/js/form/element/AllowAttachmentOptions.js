define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function ($,_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({
              /**
         * Init
         */
        initialize: function () {
            this._super();
            this.fieldDepend(this.value());
            return this;
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.fieldDepend(value);
            return this._super();
        },
        /**
         * Update field dependency
         *
         * @param {String} value
         */
        fieldDepend: function (value) {
            setTimeout(function () {
                var image = uiRegistry.get('index = attechment_file');
                if (value == 1) {
                    image.show();
                } else {
                    image.hide();
                }
            }, 1);
            return this;
        }
    });
});
