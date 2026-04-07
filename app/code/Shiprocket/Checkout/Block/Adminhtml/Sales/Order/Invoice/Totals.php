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
namespace Shiprocket\Checkout\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template\Context;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Sales\Model\Order\Invoice;

class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * Order invoice
     *
     * @var Invoice|null
     */
    protected $invoice = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $source;

    /**
     * OrderFee constructor.
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
     * Get invoice
     *
     * @return \Magento\Framework\DataObject
     */
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();

        if (!$this->getSource()->getFastrrCodCharge()) {
            return $this;
        }
        if ($this->getSource()->getFastrrCodCharge()) {
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $this->shiprocketHelper->getCodCode(),
                    'value' => $this->getSource()->getFastrrCodCharge(),
                    'label' => $this->shiprocketHelper->getCodLabel(),
                ]
            );

            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }
        return $this;
    }
}
