define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function (){

            var status = this._super().initialValue;
            this.fieldDepend(status);
            return this;
            location.reload();

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
                // forfont size
                var font_size = uiRegistry.get('index = label_fontsize');
                // for shape
                var shape_select = uiRegistry.get('index = label_shape');
                // image uploader
                var image = uiRegistry.get('index = label_img');
                // // discount grid
                var rule_type = uiRegistry.get('index = rule_type');
                // discount text
                var label_text = uiRegistry.get('index = label_text');
                // // discount text colore
                var label_color = uiRegistry.get('index = label_color');
                // // discount back ground colore
                var label_back_color = uiRegistry.get('index = label_back_color');
                // get selected rule type value
                var rule_tye = $('select[name="rule_type"] option:selected').val();
                if (value == 0) {
                    // for image uploader
                    image.show();
                    shape_select.hide();
                    font_size.hide();
                    // hide label option
                    label_text.hide();
                    label_color.hide();
                    label_back_color.hide();
                 } else {
                    // for image uploader
                    image.hide();
                    shape_select.show();
                    font_size.show();
                    //label setting
                    if (rule_tye != 1) {
                        label_text.show();
                        label_color.show();
                        label_back_color.show();
                    } else {
                        label_text.hide();
                        label_color.hide();
                        label_back_color.hide();
                    }
                 }
            }, 1000);
            return this;
        }
    });
});