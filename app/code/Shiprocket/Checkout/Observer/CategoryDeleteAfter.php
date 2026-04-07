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
namespace Shiprocket\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Framework\Event\Observer;
use Shiprocket\Checkout\Logger\SrLogger;

class CategoryDeleteAfter implements ObserverInterface
{
    /**
     * @var SrLogger
     */
    protected $srLogger;
    
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @param ShiprocketHelper  $shiprocketHelper
     * @param SrLogger          $srLogger
     */
    public function __construct(
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Delete category sync
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->shiprocketHelper->isEnabled()) {
            return false;
        }

        $category = $observer->getCategory();
        $this->srLogger->info("DeleteCategoryStarted=>".$category->getId());
        $data = [
            'id' => (int) $category->getId()
        ];
        $this->shiprocketHelper->sync($data, 'collections/delete');
    }
}
