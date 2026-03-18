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
namespace Shiprocket\Checkout\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;

class HeadContent extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @param Context           $context
     * @param ShiprocketHelper  $shiprocketHelper
     * @param array             $data
     */
    public function __construct(
        Context $context,
        ShiprocketHelper $shiprocketHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shiprocketHelper = $shiprocketHelper;
    }

    /**
     * Get plugin status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->shiprocketHelper->isEnabled();
    }

    /**
     * Get plugin mode
     *
     * @return boolean
     */
    public function isProduction()
    {
        return $this->shiprocketHelper->isProduction();
    }

    /**
     * Get checkout js
     *
     * @return string
     */
    public function getCheckoutJs()
    {
        return $this->shiprocketHelper->getCheckoutJs();
    }

    /**
     * Get checkout css
     *
     * @return string
     */
    public function getCheckoutCss()
    {
        return $this->shiprocketHelper->getCheckoutCss();
    }

    /**
     * Get promise status
     *
     * @return string
     */
    public function isPromiseEnabled()
    {
        return $this->shiprocketHelper->isPromiseEnabled();
    }

    /**
     * Get promise js
     *
     * @return string
     */
    public function getPromiseJs()
    {
        return $this->shiprocketHelper->getPromiseJs();
    }
}
