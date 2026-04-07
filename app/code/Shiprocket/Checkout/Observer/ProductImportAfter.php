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
use Magento\Store\Model\StoreManagerInterface;
use Shiprocket\Checkout\Model\ProductFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\CategoryRepository;
use Shiprocket\Checkout\Logger\SrLogger;

class ProductImportAfter implements ObserverInterface
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
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var ProductFactory
     */
    protected $srProductFactory;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param ProductRepositoryInterface        $productRepository
     * @param StockRegistryInterface            $stockRegistry
     * @param ShiprocketHelper                  $shiprocketHelper
     * @param StoreManagerInterface             $storeManager
     * @param ProductFactory                    $srProductFactory
     * @param Curl                              $curlClient
     * @param CategoryLinkManagementInterface   $categoryLinkManagement
     * @param Configurable                      $configurableType
     * @param CategoryRepository                $categoryRepository
     * @param SrLogger                          $srLogger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        ShiprocketHelper $shiprocketHelper,
        StoreManagerInterface $storeManager,
        ProductFactory $srProductFactory,
        Curl $curlClient,
        CategoryLinkManagementInterface $categoryLinkManagement,
        Configurable $configurableType,
        CategoryRepository $categoryRepository,
        SrLogger $srLogger
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->storeManager = $storeManager;
        $this->srProductFactory = $srProductFactory;
        $this->curlClient = $curlClient;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->configurableType = $configurableType;
        $this->categoryRepository = $categoryRepository;
        $this->srLogger = $srLogger;
    }

    /**
     * Sync product
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->shiprocketHelper->isEnabled()) {
            return false;
        }

        $bunch = $observer->getEvent()->getData('bunch');
        foreach ($bunch as $product) {
            $sku = $product['sku'];
            $product = $this->productRepository->get($sku);
            $productId = $product->getId();
            $this->srLogger->info("Bulk Product Update Pid=>$productId, Sku=>$sku");
            $srpModel = $this->srProductFactory->create();
            if ($productId) {
                $srModel = $srpModel->load($productId, 'pid');
                if (!$srModel->getId()) {
                    $srModel->setPid($productId);
                    $srModel->setCreatedAt(date('Y-m-d H:i:s'));
                }
                $srModel->setType('U');
                $srModel->setStatus(1);
                $srModel->setUpdatedAt(date('Y-m-d H:i:s'));
                $srModel->save();
            }
        }
    }
}
