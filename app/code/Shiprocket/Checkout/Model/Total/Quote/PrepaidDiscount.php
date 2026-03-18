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
namespace Shiprocket\Checkout\Model\Total\Quote;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Shiprocket\Checkout\Logger\SrLogger;

class PrepaidDiscount extends AbstractTotal
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
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }
        $isSrActive = $this->shiprocketHelper->getCheckoutSession()->getSrCheckoutActive();
        $prepaidDiscount = $this->shiprocketHelper->getCheckoutSession()->getSrPrepaidDiscount();
        $discountCoupon = $this->shiprocketHelper->getCheckoutSession()->getSrDiscountCoupon();
        $discountAmount = $this->shiprocketHelper->getCheckoutSession()->getSrDiscountAmount();
        $finalDiscount = 0;
        $finalDiscountDescription = '';

        $subTotal = 0;
        foreach ($items as $item) {
            $originalPrice = $item->getPrice();
            $qty = $item->getQty();
            $subTotal = $subTotal + ($originalPrice * $qty);
        }

        if ($isSrActive) {
            if ($discountAmount > 0) {
                $finalDiscount = $discountAmount;
                $finalDiscountDescription = $discountCoupon;
            }
            if ($prepaidDiscount > 0) {
                $finalDiscount = $finalDiscount + $prepaidDiscount;
                if ($discountAmount > 0) {
                    $finalDiscountDescription = $finalDiscountDescription . ', Prepaid Discount';
                } else {
                    $finalDiscountDescription = 'Prepaid Discount';
                }
            }

            if ($finalDiscount > 0) {
                $total->setTotalAmount('custom_discount', -$finalDiscount);
                $total->setBaseTotalAmount('custom_discount', -$finalDiscount);
                $total->setDiscountAmount(-$finalDiscount);
                $total->setBaseDiscountAmount(-$finalDiscount);

                $quote->setDiscountDescription($finalDiscountDescription);
                $quote->setCustomDiscount(-$finalDiscount);
                $quote->setSubtotal($quote->getSubtotal() - $finalDiscount);
                $quote->setGrandTotal($quote->getGrandTotal() - $finalDiscount);
                $quote->setBaseSubtotal($quote->getBaseSubtotal() - $finalDiscount);
                $quote->setBaseGrandTotal($quote->getBaseGrandTotal() - $finalDiscount);

                $discountsOnProduct = $this->shiprocketHelper->discountsOnProduct();
                if ($discountsOnProduct) {
                    foreach ($items as $item) {
                        $originalPrice = $item->getPrice();
                        $qty = $item->getQty();
                        $prodDiscountAmount = ($originalPrice*$qty/$subTotal)*$finalDiscount;
                        $discountedPrice = $originalPrice - $prodDiscountAmount;
            
                        //$item->setCustomPrice($discountedPrice);
                        //$item->setOriginalPrice($originalPrice);
                        $item->setDiscountAmount($prodDiscountAmount);
                        $item->setBaseDiscountAmount($prodDiscountAmount);
                        $item->setRowTotal($discountedPrice * $qty);
                        $item->setBaseRowTotal($discountedPrice * $qty);
                        $item->save();
                    }
                }
            }
        }

        return $this;
    }
}
