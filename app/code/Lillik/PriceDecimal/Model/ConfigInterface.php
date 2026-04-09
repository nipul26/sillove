<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Interface for price decimal module configuration.
 */
interface ConfigInterface
{
    /**
     * Retrieve scope config instance.
     *
     * @return ScopeConfigInterface
     */
    public function getScopeConfig(): ScopeConfigInterface;

    /**
     * Check if the module is enabled.
     *
     * @return bool
     */
    public function isEnable(): bool;
}
