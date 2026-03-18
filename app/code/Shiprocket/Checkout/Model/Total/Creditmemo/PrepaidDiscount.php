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
namespace Shiprocket\Checkout\Model\Total\Creditmemo;

use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Logger\SrLogger;
use Magento\Tax\Model\Config;
use Magento\Sales\Model\Order\Creditmemo\Total\Discount;
use Magento\Sales\Model\Order\Creditmemo;

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
     * @var Config
     */
    private $taxConfig;

    /**
     * @param ShiprocketHelper  $shiprocketHelper
     * @param SrLogger          $srLogger
     * @param Config            $taxConfig
     * @param array             $data
     */
    public function __construct(
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger,
        Config $taxConfig,
        array $data = []
    ) {
        $this->taxConfig = $taxConfig;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
        parent::__construct($taxConfig, $data);
    }

    /**
     * Collect
     *
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(
        Creditmemo $creditmemo
    ) {
        parent::collect($creditmemo);

        $finalDiscount = $creditmemo->getOrder()->getDiscountAmount();
        $finalDiscountDescription = $creditmemo->getOrder()->getDiscountDescription();

        $creditmemo->setDiscountAmount($finalDiscount);
        $creditmemo->setBaseDiscountAmount($finalDiscount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $finalDiscount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $finalDiscount);
        $creditmemo->setDiscountDescription($finalDiscountDescription);

        return $this;
    }
}
