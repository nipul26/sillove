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
                if (status == 2) {
                    // hide condition
                    setTimeout(function () {
                            // hide discount tab
                        $('div[data-index="discount_row"]').hide();
                        $('div[data-index="discount_row"]').prev().hide();
                            // hide condition
                        $('div[data-index="conditions_serialized"]').show();

                        }, 1800);
                } else if (status == 1) {

                    setTimeout(function () {
                            // hide discount tab
                        $('div[data-index="discount_row"]').show();
                        $('div[data-index="discount_row"]').prev().show();
                            // hide condition
                        $('div[data-index="conditions_serialized"]').hide();

                        }, 1800);

                } else {
                    // hide condition
                    setTimeout(function () {
                            // hide discount tab
                        $('div[data-index="discount_row"]').hide();
                        $('div[data-index="discount_row"]').prev().hide();
                            // hide condition
                        $('div[data-index="conditions_serialized"]').hide();

                        }, 1800);
                }



            this.fieldDepend(status);
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
                // image uploader
                var image = uiRegistry.get('index = label_img');
                // discount text
                var label_text = uiRegistry.get('index = label_text');
                label_text.hide();
                // discount text colore
                var label_color = uiRegistry.get('index = label_color');
                label_color.hide();
                // discount back ground colore
                var label_back_color = uiRegistry.get('index = label_back_color');
                label_back_color.hide();

                // change status of rule type
                if (value != 1) {
                    image.show();
                    // hide discount grid
                    $('div[data-index="discount_row"]').hide();
                    $('div[data-index="discount_row"]').prev().hide();
                    // show custom option
                    $('select[name="label_type"]').val(0).change();
                    $('select[name="label_type"] option[value="0"]').attr("selected",true);
                    $('select[name="label_type"] option[value="0"]').show();
                } else {
                    // show image
                    image.hide();
                    // select image type
                    $('select[name="label_type"]').val(1).change();
                    $('select[name="label_type"] option[value="1"]').attr("selected",true);
                    $('select[name="label_type"] option[value="0"]').hide();
                    // show discount grid
                    $('div[data-index="discount_row"]').show();
                    $('div[data-index="discount_row"]').prev().show();
                }
                //change on condition for categlog rule base
                if (value == 2) {
                    // hide condition
                    $('div[data-index="conditions_serialized"]').show();
                } else {
                    // hide condition
                    $('div[data-index="conditions_serialized"]').hide();
                }

            }, 100);
            return this;
        }
    });
});