require([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    $.ajax({
        url: '/srcheckout/cart/refresh', // Update this URL based on your route
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            console.log('Cart refresh Start');
            if (response.success) {
                console.log('Cart refresh If');
                var cartData = customerData.get('cart');
                try {
                    customerData.reload(['cart'], true);
                } catch (error) {
                    console.error('Error during cart reload:', error);
                }
            } else {
                console.log('Cart refresh Else');
                console.log(response.message);
            }
        },
        error: function () {
            console.log('Something went wrong. Please try again.');
        }
    });
});