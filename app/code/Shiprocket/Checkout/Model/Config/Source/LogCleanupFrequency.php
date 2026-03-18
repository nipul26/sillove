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

class LogCleanupFrequency implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * Get log cleanup option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $list = [
            ['value' => '', 'label' => 'Disabled'],
            ['value' => '7', 'label' => '7 Days'],
            ['value' => '15', 'label' => '15 Days'],
            ['value' => '30', 'label' => '30 Days'],
            ['value' => '90', 'label' => '90 Days']
        ];

        return $list;
    }
}
