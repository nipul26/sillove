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

use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Quote\Model\QuoteValidator;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Shiprocket\Checkout\Logger\SrLogger;

class CodCharge extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var QuoteValidator
     */
    protected $quoteValidator = null;
    
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param QuoteValidator    $quoteValidator
     * @param ShiprocketHelper  $shiprocketHelper
     * @param SrLogger          $srLogger
     */
    public function __construct(
        QuoteValidator $quoteValidator,
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Collect grand total address amount
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
            $codCharge = $this->shiprocketHelper->getCheckoutSession()->getSrCodPrice();
        if ($codCharge) {
            $this->srLogger->info('Cod Charge Called: '.$codCharge);
            $total->setTotalAmount($this->shiprocketHelper->getCodCode(), $codCharge);
            $total->setBaseTotalAmount($this->shiprocketHelper->getCodCode(), $codCharge);

            $total->setFastrrCodCharge($codCharge);
            $total->setBaseFastrrCodCharge($codCharge);

            if ($total->getFastrrCodCharge()) {
                $total->setGrandTotal($total->getGrandTotal());
                $total->setBaseGrandTotal($total->getBaseGrandTotal());
            } else {
                $total->setGrandTotal($total->getGrandTotal() + $codCharge);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() + $codCharge);
            }
        }

            return $this;
    }

    /**
     * Clear subtotal amount address object
     *
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
           'code'  => $this->shiprocketHelper->getCodCode(),
           'title' => $this->shiprocketHelper->getCodLabel(),
           'value' => $total->getCodCharge()
        ];
    }

     /**
      * Get Subtotal label
      *
      * @return \Magento\Framework\Phrase
      */
    public function getLabel()
    {
        return $this->shiprocketHelper->getCodLabel();
    }
}
