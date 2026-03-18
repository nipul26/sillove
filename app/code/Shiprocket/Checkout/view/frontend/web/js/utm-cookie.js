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

(function () {
    var queryParams = new URLSearchParams(window.location.search);
    queryParams.delete('q');
    queryParams.delete('p');
    queryParams.delete('pageNum');
    var utmString = Array.from(queryParams.entries())
        .map(function ([key, value]) {
            return key + '=' + value;
        })
        .join('&');

    if (utmString) {
        document.cookie = "sr_utm_data=" + encodeURIComponent(utmString) + "; path=/; max-age=2592000";
    }
})();