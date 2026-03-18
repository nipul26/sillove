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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Framework\Event\Observer;
use Shiprocket\Checkout\Logger\SrLogger;

class StockSaveAfter implements ObserverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param ProductRepositoryInterface    $productRepository
     * @param StockRegistryInterface        $stockRegistry
     * @param ShiprocketHelper              $shiprocketHelper
     * @param SrLogger                      $srLogger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Sync product stock
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->shiprocketHelper->isEnabled()) {
            return false;
        }

        $stockItem = $observer->getEvent()->getItem();
        $productId = $stockItem->getProductId();
        $product = $this->productRepository->getById($productId);
        $stockData = [];
        $stockData['product_id'] = $productId;
        $quantity = 0;
        if ($product->getTypeId() == 'simple') {
            $stockData['qty'] = $this->shiprocketHelper->getProductAvailableQty($product->getSku());
            
            $stockItem = $this->stockRegistry->getStockItem($productId);
            $stockData['is_in_stock'] = $stockItem->getIsInStock();

            $this->srLogger->info(json_encode($stockData));
            $this->shiprocketHelper->sync($stockData, 'products/quantity');
        }
    }
}
