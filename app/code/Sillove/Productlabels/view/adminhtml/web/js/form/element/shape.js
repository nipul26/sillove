define([
    'Magento_Ui/js/form/element/select',
    'jquery'
], function (select, $) {
    'use strict';
    return select.extend({
    	initialize: function () {
            this._super();
            setTimeout(function() {
                $("div#shapes").addClass($('.myshapes').find(":selected").text().toLowerCase());
            }, 1500);
        },
        initShapePickerCall: function () {
        	$(function() {
    			var prevSelect = '';
    			var thisSelect = $(this).find(":selected").text().toLowerCase();
    			$("select.myshapes").change(function(){
                    $("div#shapes").removeAttr('class');
		            prevSelect = thisSelect;
        			thisSelect = $(this).find(":selected").text().toLowerCase();
        			$("div#shapes").removeClass(prevSelect).addClass(thisSelect);
		        });
		    });
            return this;
        }
	});
});