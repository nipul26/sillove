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
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Client\Curl;
use Shiprocket\Checkout\Logger\SrLogger;

class CategorySaveAfter implements ObserverInterface
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
     * @var CategoryFactory
     */
    protected $categoryFactory;
    
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;
    
    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param ShiprocketHelper      $shiprocketHelper
     * @param CategoryFactory       $categoryFactory
     * @param CollectionFactory     $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param IndexerRegistry       $indexerRegistry
     * @param CategoryRepository    $categoryRepository
     * @param Curl                  $curlClient
     * @param SrLogger              $srLogger
     */
    public function __construct(
        ShiprocketHelper $shiprocketHelper,
        CategoryFactory $categoryFactory,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        IndexerRegistry $indexerRegistry,
        CategoryRepository $categoryRepository,
        Curl $curlClient,
        SrLogger $srLogger
    ) {
        $this->shiprocketHelper = $shiprocketHelper;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->indexerRegistry = $indexerRegistry;
        $this->categoryRepository = $categoryRepository;
        $this->curlClient = $curlClient;
        $this->srLogger = $srLogger;
    }

    /**
     * Sync category
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->shiprocketHelper->isEnabled()) {
            return false;
        }

        $cat = $observer->getCategory();
        $categoryId = $cat->getId();
        $action = 'collections/update';
        $isNew = false;
        if ($cat->isObjectNew()) {
            $action = 'collections/create';
            $this->processCategoryIndexing();
            $isNew = true;
        }

        $this->srLogger->info($action." Started=>".$categoryId);

        $category = $this->categoryFactory->create()->load($categoryId);

        $catProducts = $category->getProductCollection()->addAttributeToSelect('*');

        $productsArray = [];
        $i = 0;
        foreach ($catProducts as $product) {
            $productsArray[$i]['product_id'] = (int) $product->getEntityId();
            $productsArray[$i]['sku'] = $product->getSku();
            $productsArray[$i]['position'] = $product->getCatIndexPosition();
            $i++;
        }
        $catArray = $this->getCategoryData($categoryId, $isNew);
        $catArray['name'] = $category->getName();
        $catArray['is_active'] = ($category->getIsActive())? true : false;
        $data = [
            'category' => $catArray,
            'products' => $productsArray
        ];
        $this->srLogger->info('data: '. json_encode($data));
        $this->shiprocketHelper->sync($data, $action);
    }

    /**
     * Fetch category data
     *
     * @param int $categoryId
     * @param boolean $isNew
     * @return string
     */
    public function getCategoryData($categoryId, $isNew = false)
    {
        $response = [];
        if ($isNew) {
            $category = $this->categoryRepository->get($categoryId);
            $response["id"] = (int) $category->getEntityId();
            $response["parent_id"] = (int) $category->getParentId();
            $response["name"] = $category->getName();
            $response["is_active"] = ($category->getIsActive())? true : false;
            $response["position"] = (int) $category->getPosition();
            $response["level"] = (int) $category->getLevel();
            $response["children"] = "";
            $response["created_at"] = $category->getCreatedAt();
            $response["updated_at"] = $category->getUpdatedAt();
            $response["path"] = $category->getPath();
            $response["include_in_menu"] = ($category->getIncludeInMenu())? true : false;
            $response["custom_attributes"] = [];
        } else {
            $token = $this->shiprocketHelper->getWebhookToken();
            $accessToken = $token['token'];
            $requestUrl = $this->storeManager->getStore()->getBaseUrl()."rest/V1/categories/$categoryId";
            try {
                $header = [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' =>  'application/json'
                ];
                $this->curlClient->setHeaders($header);
                $this->curlClient->get($requestUrl);
                $responseBody = $this->curlClient->getBody();
                $response = json_decode($responseBody, true);
                if ($this->curlClient->getStatus() != 200) {
                    if (isset($response['message'])) {
                        $this->srLogger->info("cUrl Error: ".$response['message']);
                    }
                }
                $this->srLogger->info("Category Data: ".$responseBody);
            } catch (Exception $e) {
                $this->srLogger->info('Error: '.$e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Process category indexing
     *
     * @return void
     */
    public function processCategoryIndexing()
    {
        $categoryIndexer = $this->indexerRegistry->get('catalog_category_product');
        try {
            $categoryIndexer->reindexRow('catalog_category_product');
        } catch (\Exception $e) {
            $this->srLogger->error('Category Indexing Error: '.$e->getMessage());
        }
    }
}
