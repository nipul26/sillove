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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Model\ResourceModel\Product\CollectionFactory;
use Shiprocket\Checkout\Model\Product as SrProduct;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\CategoryRepository;
use Shiprocket\Checkout\Logger\SrLogger;

class SyncProduct
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var CollectionFactory
     */
    protected $srProductCollectionFactory;

    /**
     * @var SrProduct
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
     * @param StoreManagerInterface             $storeManager
     * @param ResourceConnection                $resourceConnection
     * @param ShiprocketHelper                  $shiprocketHelper
     * @param CollectionFactory                 $srProductCollectionFactory
     * @param SrProduct                         $srProduct
     * @param Curl                              $curlClient
     * @param CategoryLinkManagementInterface   $categoryLinkManagement
     * @param Configurable                      $configurableType
     * @param CategoryRepository                $categoryRepository
     * @param SrLogger                          $srLogger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        ShiprocketHelper $shiprocketHelper,
        CollectionFactory $srProductCollectionFactory,
        SrProduct $srProduct,
        Curl $curlClient,
        CategoryLinkManagementInterface $categoryLinkManagement,
        Configurable $configurableType,
        CategoryRepository $categoryRepository,
        SrLogger $srLogger
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srProductCollectionFactory = $srProductCollectionFactory;
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
     * @return $this
     */
    public function execute()
    {
        $collections = $this->srProductCollectionFactory->create()->addFieldToFilter('status', 1);
        if ($collections->count()) {
            $action = 'products/update';
            foreach ($collections as $srP) {
                $items = [];
                if ($srP->getType() == 'N') {
                    $action = 'products/create';
                }
                $product = $this->productRepository->getById($srP->getPid());
                $prodData = $this->getProductData($product->getSku());
                $quantity = $product->getExtensionAttributes()->getStockItem()->getQty();
    
                if ($product->getTypeId() == 'simple') {
                    $salableQty = $this->shiprocketHelper->getProductAvailableQty($product->getSku());
                    $prodData['extension_attributes']['stock_item']['qty'] = $salableQty;
                }
                $items['items'][] = $prodData;
                $syncStatus = $this->shiprocketHelper->sync($items, $action);
                if ($syncStatus) {
                    $srP->setStatus(0)->setUpdatedAt(date('Y-m-d H:i:s'))->save();
                }
            }
        }

        return $this;
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
        } catch (Exception $e) {
            $this->srLogger->info('Product fetch error: '.$e->getMessage());
        }

        return $response;
    }
}
