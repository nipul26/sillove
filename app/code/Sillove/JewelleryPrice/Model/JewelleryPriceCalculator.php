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

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get calculated price. Returns null if not a jewellery item or config missing.
     *
     * @param float $silverWeight Product's silver weight
     * @param float $labor Product's labor charge (required, per product)
     * @param string|int|null $storeId Store scope for silver rate config
     * @return float|null
     */
    public function calculate(float $silverWeight, float $labor, $storeId = null): ?float
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

        return ($labor + $silverRate) * $silverWeight;
    }
}
