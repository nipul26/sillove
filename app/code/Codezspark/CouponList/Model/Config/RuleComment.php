<?php

namespace Codezspark\CouponList\Model\Config;

use Magento\Framework\UrlInterface;
use Magento\Config\Model\Config\CommentInterface;

class RuleComment implements CommentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * Constrcuctor
     *
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    /**
     * Generate comment text to display.
     *
     * @param mixed $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        $url = $this->urlInterface->getUrl('sales_rule/promo_quote/index');

        return 'Display coupons that enable <b>Show In Coupon List</b> from <a href="' .
            $url . '"target="_blank">Sales Rules</a>.';
    }
}
