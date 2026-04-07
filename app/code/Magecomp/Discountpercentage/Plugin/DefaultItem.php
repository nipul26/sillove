<?php

namespace Magecomp\Discountpercentage\Plugin;

use Magento\Framework\Pricing\PriceCurrencyInterface as CurrencyInterface;
use Magento\Quote\Model\Quote\Item;

class DefaultItem
{
    /**
     * @var CurrencyInterface
     */
    protected $currencyInterface;

    public function __construct(
        CurrencyInterface $currencyInterface
    ) {
        $this->currencyInterface = $currencyInterface;
    }

    /**
     * Plugin on the PUBLIC method getItemData (not the protected doGetItemData).
     */
    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        array $result,
        Item $item
    ) {
        $product = $item->getProduct();
        $discount = 0;
        $save = 0;

        $currentPrice = (float)$product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $originalPrice = (float)$product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();

        if ($originalPrice > 0 && $currentPrice < $originalPrice) {
            $discount = round((($originalPrice - $currentPrice) / $originalPrice) * 100, 0);
            $save = $originalPrice - $currentPrice;
        }

        $result['regular_price_value'] = $originalPrice;
        $result['regular_price'] = $this->currencyInterface->format($originalPrice, false, 2);
        $result['discount_percentage'] = $discount;
        $result['saved_amount'] = $this->currencyInterface->format($save, false, 2);

        return $result;
    }
}