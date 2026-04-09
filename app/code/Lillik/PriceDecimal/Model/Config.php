<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Price decimal module configuration model.
 *
 * Provides access to module settings stored in system configuration.
 */
class Config implements ConfigInterface
{
    public const XML_PATH_PRICE_PRECISION = 'catalog_price_decimal/general/price_precision';

    public const XML_PATH_CAN_SHOW_PRICE_DECIMAL = 'catalog_price_decimal/general/can_show_decimal';

    public const XML_PATH_GENERAL_ENABLE = 'catalog_price_decimal/general/enable';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getScopeConfig(): ScopeConfigInterface
    {
        return $this->scopeConfig;
    }

    /**
     * Return config value by XML path.
     *
     * @param string $path
     * @param string $scopeType
     * @return mixed
     */
    public function getValueByPath(string $path, string $scopeType = 'website')
    {
        return $this->getScopeConfig()->getValue($path, $scopeType);
    }

    /**
     * @inheritDoc
     */
    public function isEnable(): bool
    {
        return (bool) $this->getValueByPath(self::XML_PATH_GENERAL_ENABLE, 'website');
    }

    /**
     * Check if decimal display is allowed.
     *
     * @return bool
     */
    public function canShowPriceDecimal(): bool
    {
        return (bool) $this->getValueByPath(self::XML_PATH_CAN_SHOW_PRICE_DECIMAL, 'website');
    }

    /**
     * Return price precision value from store config.
     *
     * @return int
     */
    public function getPricePrecision(): int
    {
        return (int) $this->getValueByPath(self::XML_PATH_PRICE_PRECISION, 'website');
    }
}
