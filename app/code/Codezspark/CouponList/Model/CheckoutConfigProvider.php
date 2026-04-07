<?php

namespace Codezspark\CouponList\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Codezspark\CouponList\Block\Cart\CouponList;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * Getting CouponList
     *
     * @var CouponList
     */
    protected $couponList;

    /**
     *
     * @param CouponList $couponList
     */
    public function __construct(
        CouponList $couponList
    ) {
        $this->couponList = $couponList;
    }

    public function getConfig()
    {
        $config['couponlist'] = [
               'moduleStatus' => (int) $this->couponList->getListHelper()->getModuleStatusConfig(),
               'listCount' => count($this->couponList->getCouponList())
        ];

        return $config;
    }
}
