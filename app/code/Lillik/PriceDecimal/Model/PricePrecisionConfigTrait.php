<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model;

/**
 * Provides price precision configuration access to classes using this trait.
 *
 * Classes using this trait must define a `$moduleConfig` property of type ConfigInterface.
 */
trait PricePrecisionConfigTrait
{
    /**
     * Retrieve module configuration instance.
     *
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->moduleConfig;
    }

    /**
     * Get the configured price precision.
     *
     * Returns 0 when decimal display is disabled.
     *
     * @return int
     */
    public function getPricePrecision(): int
    {
        if ($this->getConfig()->canShowPriceDecimal()) {
            return $this->getConfig()->getPricePrecision();
        }

        return 0;
    }
}
