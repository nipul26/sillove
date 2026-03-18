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

use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * @var CollectionFactory
     */
    protected $statusCollectionFactory;
    
    /**
     * @param CollectionFactory $statusCollectionFactory
     */
    public function __construct(
        CollectionFactory $statusCollectionFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Get order status option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statusCollection = $this->statusCollectionFactory->create();

        $statuses = [];
        foreach ($statusCollection as $status) {
            $statuses[] = [
                'value' => $status->getStatus(),
                'label' => $status->getLabel(),
            ];
        }

        return $statuses;
    }
}
