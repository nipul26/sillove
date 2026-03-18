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
use Shiprocket\Checkout\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\CategoryRepository;
use Shiprocket\Checkout\Logger\SrLogger;

class ProductSaveAfter implements ObserverInterface
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
     * @var Product
     */
    protected $srProduct;

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
     * @param Product                           $srProduct
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
        Product $srProduct,
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
        $this->srProduct = $srProduct;
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

        $product = $observer->getProduct();
        $productId = $product->getId();
        $action = 'products/update';
        if ($product->isObjectNew()) {
            $action = 'products/create';
            $srModel = $this->srProduct->load($productId, 'pid');
            if (!$srModel->getEntityId()) {
                $srModel->setPid($productId);
            }
            $srModel->setType('N');
            $srModel->setStatus(1);
            $srModel->setCreatedAt(date('Y-m-d H:i:s'));
            $srModel->setUpdatedAt(date('Y-m-d H:i:s'));
            $srModel->save();
        } else {
            $srModel = $this->srProduct->load($productId, 'pid');
            if (!$srModel->getEntityId()) {
                $srModel->setPid($productId);
            } else {
                $srModel->setCreatedAt(date('Y-m-d H:i:s'));
            }
            $srModel->setType('U');
            $srModel->setStatus(1);
            $srModel->setUpdatedAt(date('Y-m-d H:i:s'));
            $srModel->save();

            $product = $this->productRepository->getById($productId, false, null, true);
            $prodData = $this->getProductData($product->getSku());
            $quantity = $product->getExtensionAttributes()->getStockItem()->getQty();

            if ($product->getTypeId() == 'simple') {
                $quantity = $this->shiprocketHelper->getProductAvailableQty($product->getSku());
                $prodData['extension_attributes']['stock_item']['qty'] = $quantity;
                $prodData['extension_attributes']['qty'] = $quantity;
            }
            $items['items'][] = $prodData;
            $this->srLogger->info($action." started with Product ID: $productId");
            $this->shiprocketHelper->sync($items, $action);
        }
    }

    /**
     * Fetch product data
     *
     * @param string $sku
     * @return string
     */
    public function getProductData($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            $response = [
                'id' => (int) $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'attribute_set_id' => (int) $product->getAttributeSetId(),
                'price' => $product->getPrice(),
                'status' => (int) $product->getStatus(),
                'visibility' => (int) $product->getVisibility(),
                'type_id' => $product->getTypeId(),
                'created_at' => $product->getCreatedAt(),
                'updated_at' => $product->getUpdatedAt(),
                'weight' => $product->getWeight(),
                'extension_attributes' => [
                    'website_ids' => (int) $product->getWebsiteIds(),
                    'category_links' => [],
                    'qty' => null,
                ],
                'product_links' => [],
                'options' => [],
                'media_gallery_entries' => [],
                'tier_prices' => [],
                'custom_attributes' => [],
            ];

            $categoryIds = $product->getCategoryIds(); // Get category IDs
            foreach ($categoryIds as $categoryId) {
                try {
                    $category = $this->categoryRepository->get($categoryId); // Load category details
                    $response['extension_attributes']['category_links'][] = [
                        'position' => 0,
                        'category_id' => $category->getId(),
                    ];
                } catch (NoSuchEntityException $e) {
                    $this->srLogger->info('Category load error: '.$e->getMessage());
                }
            }
            
            $stockItem = $product->getExtensionAttributes()->getStockItem();
            if ($stockItem) {
                $response['extension_attributes']['qty'] = $stockItem->getQty();
            }
            
            if ($product->getTypeId() === 'configurable') {
                $attributes = $this->configurableType->getConfigurableAttributes($product);
            
                foreach ($attributes as $attribute) {
                    $attributeId = $attribute->getAttributeId();
                    $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
                    $attributeLabel = $attribute->getLabel();
                    $options = [];
            
                    foreach ($attribute->getOptions() as $option) {
                        $options[] = [
                            'value_index' => (int) $option['value_index']
                        ];
                    }
            
                    $response['configurable_product_options'][] = [
                        'id' => (int) $attribute->getId(),
                        'attribute_id' => $attributeId,
                        'label' => $attributeLabel,
                        'position' => (int) $attribute->getPosition(),
                        'values' => $options,
                        'product_id' => (int) $product->getId(),
                        'attribute_code' => $attributeCode,
                    ];
                }
            
                $childProductIds = $this->configurableType->getChildrenIds($product->getId());
                if (!empty($childProductIds[0])) {
                    $response['configurable_product_links'] = array_values($childProductIds[0]);
                }
            }
            
            if ($product->getMediaGalleryEntries()) {
                foreach ($product->getMediaGalleryEntries() as $entry) {
                    $response['media_gallery_entries'][] = $entry->getData();
                }
            }
            
            if ($product->getProductLinks()) {
                foreach ($product->getProductLinks() as $link) {
                    $response['product_links'][] = $link->getData();
                }
            }
            
            foreach ($product->getCustomAttributes() as $attribute) {
                $response['custom_attributes'][] = [
                    'attribute_code' => $attribute->getAttributeCode(),
                    'value' => $attribute->getValue(),
                ];
            }
            //$this->srLogger->info("Product Data: ".json_encode($response));
        } catch (Exception $e) {
            $this->srLogger->info('Product fetch error: '.$e->getMessage());
        }

        return $response;
    }
}
