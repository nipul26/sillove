<?php

namespace Sillove\JewelleryPrice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product;
use Sillove\JewelleryPrice\Model\JewelleryPriceCalculator;

/**
 * Sets jewellery price on product before save so it is stored in the database.
 */
class ProductSaveBeforeObserver implements ObserverInterface
{
    /**
     * @var JewelleryPriceCalculator
     */
    private $priceCalculator;

    public function __construct(JewelleryPriceCalculator $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        if (!$product) {
            return;
        }

        $silverWeight = (float)$product->getData('silver_weight');
        $labor = (float)$product->getData('labor_charge');

        if ($silverWeight <= 0 || $labor <= 0) {
            return;
        }

        $storeId = $product->getStoreId();
        if ($storeId === null) {
            $storeId = 0;
        }

        $price = $this->priceCalculator->calculate($silverWeight, $labor, $storeId);
        if ($price !== null) {
            $product->setPrice($price);
        }
    }
}
