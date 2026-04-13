<?php

namespace Magecomp\Discountpercentage\Plugin;

use Magecomp\Discountpercentage\Helper\Data as DiscountHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class CartItemPrice
{
    /**
     * @var DiscountHelper
     */
    private $discountHelper;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        DiscountHelper $discountHelper,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->discountHelper = $discountHelper;
        $this->priceCurrency = $priceCurrency;
    }

    public function afterGetUnitPriceHtml(
        \Magento\Checkout\Block\Cart\Item\Renderer $subject,
        string $result,
        AbstractItem $item
    ): string {
        if (!$this->discountHelper->isActive()) {
            return $result;
        }

        $product = $item->getProduct();
        $regularPrice = (float)$product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $finalPrice = (float)$product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        if ($regularPrice <= 0 || $finalPrice <= 0 || $finalPrice >= $regularPrice) {
            return $result;
        }

        $discountPercent = (int)round((($regularPrice - $finalPrice) / $regularPrice) * 100);
        $formattedRegularPrice = $this->priceCurrency->format($regularPrice, false, 2);

        $discountHtml = '<div class="discount-info" style="margin-top:4px;">'
            . ' <span class="old-price">'
            . ' <span class="price">'
            . $formattedRegularPrice
            . '</span>'
            . '</span>'
            . '<span class="discount-badge">'
            . $discountPercent . '% ' . __('off')
            . '</span>'
            . '</div>';

        return $result . $discountHtml;
    }
}
