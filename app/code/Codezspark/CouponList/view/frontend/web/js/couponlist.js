require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function($, modal) {
        $(document).on('click', '#openModel', function(event){
            var options = {
                type: 'slide',
                responsive: true,
                innerScroll: true,
                title: 'Available Coupons',
                modalClass: 'couponlist'
            };
            modal(options, $('#coupon-model'));
            $('#coupon-model').modal('openModal');
        });
    }
);
