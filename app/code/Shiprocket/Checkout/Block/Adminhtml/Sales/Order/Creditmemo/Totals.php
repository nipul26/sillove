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
namespace Shiprocket\Checkout\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Framework\View\Element\Template\Context;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Framework\DataObject;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Order creditmemo
     *
     * @var Creditmemo|null
     */
    protected $creditmemo = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $source;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * OrderFee constructor
     *
     * @param Context $context
     * @param ShiprocketHelper $shiprocketHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ShiprocketHelper $shiprocketHelper,
        array $data = []
    ) {
        $this->shiprocketHelper = $shiprocketHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get creditmemo
     *
     * @return \Magento\Framework\DataObject
     */
    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }
    
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();

        if (!$this->getSource()->getFastrrCodCharge()) {
            return $this;
        }
        
        if (!$this->getSource()->getFastrrCodCharge()) {
            $codCharge = new \Magento\Framework\DataObject(
                [
                    'code' => $this->shiprocketHelper->getCodCode(),
                    'strong' => false,
                    'value' => $this->getSource()->getFastrrCodCharge(),
                    'label' => $this->shiprocketHelper->getCodLabel(),
                ]
            );
            $this->getParentBlock()->addTotalBefore($codCharge, 'grand_total');
        }

        return $this;
    }
}
