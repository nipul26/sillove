<?php
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
declare(strict_types=1);
namespace Shiprocket\Checkout\Model\Total\Invoice;

use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Logger\SrLogger;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\Discount;

class PrepaidDiscount extends Discount
{
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param ShiprocketHelper  $shiprocketHelper
     * @param SrLogger          $srLogger
     */
    public function __construct(
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Collect
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(
        Invoice $invoice
    ) {
        parent::collect($invoice);
        $order = $invoice->getOrder();
        $finalDiscount = $order->getDiscountAmount();
        $finalDiscountDescription = $order->getDiscountDescription();

        $invoice->setDiscountAmount($finalDiscount);
        $invoice->setBaseDiscountAmount($finalDiscount);
        $invoice->setGrandTotal($order->getGrandTotal());
        $invoice->setBaseGrandTotal($order->getBaseGrandTotal());
        $invoice->setDiscountDescription($finalDiscountDescription);

        return $this;
    }
}
