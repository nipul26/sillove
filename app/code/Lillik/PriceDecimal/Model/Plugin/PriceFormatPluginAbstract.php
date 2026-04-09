<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model\Plugin;

use Lillik\PriceDecimal\Model\ConfigInterface;
use Lillik\PriceDecimal\Model\PricePrecisionConfigTrait;

/**
 * Abstract base class for price format plugins.
 *
 * Provides shared configuration access for all price-related plugins.
 */
abstract class PriceFormatPluginAbstract
{
    use PricePrecisionConfigTrait;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $moduleConfig;

    /**
     * @param ConfigInterface $moduleConfig
     */
    public function __construct(
        ConfigInterface $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }
}
