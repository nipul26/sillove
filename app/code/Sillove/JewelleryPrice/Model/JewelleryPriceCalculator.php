<?php

namespace Sillove\JewelleryPrice\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Calculates jewellery price: (Labor + Silver Rate) × Silver Weight
 * Labor is per product; Silver Rate is from admin config.
 */
class JewelleryPriceCalculator
{
    private const XML_PATH_SILVER_RATE = 'jewellery/general/silver_rate';
    private const XML_PATH_DISCOUNT_PERCENTAGE = 'jewellery/general/discount_percentage';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get calculated prices. Returns array with price and special_price (or null).
     *
     * @param float $silverWeight Product's silver weight
     * @param float $labor Product's labor charge (required, per product)
     * @param string|int|null $storeId Store scope for silver rate config
     * @return array|null
     */
    public function calculate(float $silverWeight, float $labor, $storeId = null): ?array
    {
        if ($silverWeight <= 0 || $labor < 0) {
            return null;
        }

        $silverRate = (float)$this->scopeConfig->getValue(
            self::XML_PATH_SILVER_RATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($silverRate <= 0) {
            return null;
        }

        $basePrice = ($labor + $silverRate) * $silverWeight;

        $discountPercentage = (float)$this->scopeConfig->getValue(
            self::XML_PATH_DISCOUNT_PERCENTAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $discountedPrice = $basePrice;
        if ($discountPercentage > 0) {
            $discountedPrice = $basePrice * (1 - ($discountPercentage / 100));
        }

        // Apply marketing rounding: round to nearest 9 below the current price
        // Example: 4750 -> 4749
        $specialPrice = (floor(($discountedPrice + 1) / 10) * 10) - 1;

        return [
            'price' => round($basePrice, 2),
            'special_price' => round($specialPrice, 2)
        ];
    }
}
