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
namespace Shiprocket\Checkout\Model\Config\Source;

class IntegrationType implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * Get integration type option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $list = [
            ['value' => '1', 'label' => 'Default'],
            ['value' => '0', 'label' => 'Custom']
        ];

        return $list;
    }
}
