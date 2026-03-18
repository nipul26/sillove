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
namespace Shiprocket\Checkout\Model\ResourceModel;

class OrderLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * @var string
     */
    protected $_mainTable = 'srcheckout_order_log';

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Initilize order log
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init($this->_mainTable, $this->_idFieldName);
    }
}
