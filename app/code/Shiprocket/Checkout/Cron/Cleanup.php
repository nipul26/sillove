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
namespace Shiprocket\Checkout\Cron;

use Magento\Framework\App\ResourceConnection;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Logger\SrLogger;

class Cleanup
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ShiprocketHelper $shiprocketHelper
     * @param SrLogger $srLogger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Execute cleanup cron
     *
     * @return $this
     */
    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $logTable = $this->resourceConnection->getTableName('srcheckout_order_log');
        $frequency = $this->shiprocketHelper->getOrderLogCleanupFrequency();
        if ($frequency) {
            $connection->delete(
                $logTable,
                "created_at < date_sub(CURDATE(), INTERVAL $frequency Day)"
            );
            $this->srLogger->info('Shiprocket_Checkout_Cron_Cleanup');
        }
        return $this;
    }
}
