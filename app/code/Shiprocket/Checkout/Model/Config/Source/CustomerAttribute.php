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

use Magento\Customer\Model\CustomerFactory;

class CustomerAttribute implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerFactory $customerFactory
    ) {
        $this->customerFactory = $customerFactory;
    }
    
    /**
     * Get customer attribute as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $customer = $this->customerFactory->create();
        $attributes = $customer->getAttributes();
        $list = [];
        $list[] = ['value' => '', 'label' => 'Select'];
        foreach ($attributes as $a) {
            if (($a->getFrontendInput() == 'text') &&
            ($a->getAttributeCode() == 'email' || str_contains($a->getAttributeCode(), 'mobile'))) {
                $list[] = ['value' => $a->getAttributeCode(), 'label' => $a->getFrontendLabel()];
            }
        }

        return $list;
    }
}
