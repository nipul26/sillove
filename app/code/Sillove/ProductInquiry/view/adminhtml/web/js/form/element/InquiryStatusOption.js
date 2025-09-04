define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';
    return select.extend({
        initialize: function (){
            var field1 = uiRegistry.get('index = inquiry_replay');
            var status = this._super().initialValue;
            if ((status == 2)||(status == 4)||(status == 5))
            {
                field1.show();
            } else{
               field1.hide();
            }
            return this;
        },
        onUpdate: function (value) {
            var field1 = uiRegistry.get('index = inquiry_replay');
            if ((value == 2)||(value == 4)||(value == 5))
            {
                field1.show();
            } else {
                field1.hide();
            }
            return this._super();
        },
    });
});