/**
 * Shiprocket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the shiprocket.in license that is
 * available through the world-wide-web at this URL:
 * https://checkout.shiprocket.in/magento-license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Shiprocket
 * @package     Shiprocket_Checkout
 * @copyright   Copyright (c) Shiprocket (https://www.shiprocket.in/)
 * @license     https://checkout.shiprocket.in/magento-license
 */
require([
    'jquery'
], function ($) {
    $('#shiprocket_checkout_customer_customerattribute').on('change', function () {
        var selectedValue = $(this).val();
        if (selectedValue !== 'email') {
            $('#shiprocket_checkout_customer_mobileprefix').closest('tr').show();
        } else {
            $('#shiprocket_checkout_customer_mobileprefix').closest('tr').hide();
        }
    });
    $('#shiprocket_checkout_customer_customerattribute').trigger('change');
    var banner = '<td colspan="3"><div style="text-align: center;"><a href="https://docs.google.com/forms/d/1JPKkzdeDQNyrFZaL7c60BaIkpbtF3LOCbLKb68LMB6s/" target="_blank"><img src="https://xank-cdn.s3.ap-southeast-1.amazonaws.com/shiprocket-contactus.png"></a></div></td>';
    $("#row_shiprocket_checkout_integration_msg").html(banner);

    var feature = '<td colspan="3"><div style="background-color: #f1f1f1;padding: 20px;font-size: 15px;"><div>Help us improve the plugin! Share your ideas, vote on upcoming features, or report issues directly through our <a href="https://checkout-magento.canny.io/feature-requests" target="_blank">Feature Request Board</a></div><div>Your feedback shapes the future of this plugin. We review submissions regularly and integrate top-voted ideas into our roadmap.</div></div></td>';
    $("#row_shiprocket_checkout_features_msg").html(feature);
});