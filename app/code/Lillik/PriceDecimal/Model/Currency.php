<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Currency as MagentoCurrency;
use Magento\Framework\CurrencyInterface;

/**
 * Custom currency model with configurable price precision.
 */
class Currency extends MagentoCurrency implements CurrencyInterface
{
    use PricePrecisionConfigTrait;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $moduleConfig;

    /**
     * @param CacheInterface $appCache
     * @param ConfigInterface $moduleConfig
     * @param array|string|null $options
     * @param string|null $locale
     */
    public function __construct(
        CacheInterface $appCache,
        ConfigInterface $moduleConfig,
        $options = null,
        $locale = null
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct($appCache, $options, $locale);
    }
}
