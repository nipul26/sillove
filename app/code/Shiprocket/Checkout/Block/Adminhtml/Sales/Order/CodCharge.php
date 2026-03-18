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
namespace Shiprocket\Checkout\Block\Adminhtml\Sales\Order;

use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Tax\Model\Config;

class CodCharge extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $source;

    /**
     * @param Context $context
     * @param Config $taxConfig
     * @param ShiprocketHelper $shiprocketHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $taxConfig,
        ShiprocketHelper $shiprocketHelper,
        array $data = []
    ) {
        $this->config = $taxConfig;
        $this->shiprocketHelper = $shiprocketHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get order store
     *
     * @return \Magento\Framework\DataObject
     */
    public function getStore()
    {
        return $this->order->getStore();
    }

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get label properties
     *
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get value properties
     *
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
    
    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $store = $this->getStore();
        
        if ($this->source->getFastrrCodCharge()) {
            $codCharge = new \Magento\Framework\DataObject(
                [
                        'code'      => $this->shiprocketHelper->getCodCode(),
                        'strong'    => false,
                        'value'     => $this->source->getFastrrCodCharge(),
                        'label'     => $this->shiprocketHelper->getCodLabel()
                    ]
            );
        
            $parent->addTotal($codCharge, $this->shiprocketHelper->getCodCode());
        }

        return $this;
    }
}
