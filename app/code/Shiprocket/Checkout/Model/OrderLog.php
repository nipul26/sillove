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
namespace Shiprocket\Checkout\Model;

use Shiprocket\Checkout\Model\ResourceModel\OrderLog as SrOrderLog;

class OrderLog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var CACHE_TAG values
     */
    protected const CACHE_TAG = 'srcheckout_order_log';

    /**
     * @var $_cacheTag values
     */
    protected $_cacheTag = 'srcheckout_order_log';

    /**
     * @var $_eventPrefix values
     */
    protected $_eventPrefix = 'srcheckout_order_log';

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Initialize class
     */
    protected function _construct()
    {
        $this->_init(SrOrderLog::class);
    }

    /**
     * Get cache identities
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }
}
