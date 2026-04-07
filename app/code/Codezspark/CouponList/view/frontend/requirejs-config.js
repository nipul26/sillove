var config = {
    map: {
        '*': {
            couponlist: 'Codezspark_CouponList/js/couponlist',
            'Magento_SalesRule/js/action/set-coupon-code':'Codezspark_CouponList/js/action/set-coupon-code',
            'Magento_SalesRule/js/action/cancel-coupon':'Codezspark_CouponList/js/action/cancel-coupon',
        }
    },
    shim: {
        'couponlist': {
            deps: ['jquery']
        }
    }
};
