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
namespace Shiprocket\Checkout\Model\ResourceModel\OrderLog;

use Shiprocket\Checkout\Model\OrderLog;
use Shiprocket\Checkout\Model\ResourceModel\OrderLog as SrOrderLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var const $_idFieldName
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var const $_eventPrefix
     */
    protected $_eventPrefix = 'srcheckout_order_log_collection';

    /**
     * @var const $_eventObject
     */
    protected $_eventObject = 'srcheckout_order_log_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OrderLog::class, SrOrderLog::class);
    }
}
