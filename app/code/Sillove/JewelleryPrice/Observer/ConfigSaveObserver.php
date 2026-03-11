<?php

namespace Sillove\JewelleryPrice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Sillove\JewelleryPrice\Model\JewelleryPriceCalculator;
use Psr\Log\LoggerInterface;

/**
 * When Silver Rate config is saved, updates all jewellery product prices in the database
 * and invalidates the price index so updated prices show on the storefront.
 */
class ConfigSaveObserver implements ObserverInterface
{
    /**
     * @var JewelleryPriceCalculator
     */
    private $priceCalculator;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        JewelleryPriceCalculator $priceCalculator,
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        IndexerRegistry $indexerRegistry,
        LoggerInterface $logger
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->indexerRegistry = $indexerRegistry;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore();
        if ($storeId === '' || $storeId === null) {
            $storeId = 0;
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['labor_charge', 'silver_weight', 'sku']);
        $collection->addAttributeToFilter('silver_weight', ['gt' => 0]);
        $collection->addAttributeToFilter('labor_charge', ['gt' => 0]);
        $collection->setStoreId($storeId);

        $updated = 0;
        foreach ($collection as $product) {
            try {
                $silverWeight = (float)$product->getData('silver_weight');
                $labor = (float)$product->getData('labor_charge');

                if ($silverWeight <= 0 || $labor <= 0) {
                    continue;
                }

                $price = $this->priceCalculator->calculate($silverWeight, $labor, $storeId);
                if ($price !== null) {
                    $product->setPrice($price);
                    $this->productRepository->save($product);
                    $updated++;
                }
            } catch (\Throwable $e) {
                $this->logger->warning('JewelleryPrice: failed to update product ' . $product->getSku() . ': ' . $e->getMessage());
            }
        }

        if ($updated > 0) {
            $this->logger->info(sprintf('JewelleryPrice: updated %d product(s) after config save.', $updated));
            $this->indexerRegistry->get(Processor::INDEXER_ID)->invalidate();
        }
    }
}
