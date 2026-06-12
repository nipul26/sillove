define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data'
], function ($, modal, customerData) {
    'use strict';

    return function (config, element) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Customized Jewellery Request',
            buttons: []
        };

        var popup = modal(options, $(config.modalSelector));

        $(document).on('click', config.buttonSelector, function (e) {
            e.preventDefault();
            
            // Autofill customer data before opening modal
            var customer = customerData.get('customer')();
            if (customer && customer.firstname) {
                $(config.modalSelector).find('input[name="firstname"]').val(customer.firstname);
                $(config.modalSelector).find('input[name="lastname"]').val(customer.lastname);
                $(config.modalSelector).find('input[name="email"]').val(customer.email);
                // Mobile number handle - optional depending on where it's stored in customerData
                if (customer.mobile_number) {
                    $(config.modalSelector).find('input[name="mobile"]').val(customer.mobile_number);
                }
            }

            $(config.modalSelector).modal('openModal');
        });
    };
});

