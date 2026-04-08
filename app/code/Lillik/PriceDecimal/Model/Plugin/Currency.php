<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model\Plugin;

use Lillik\PriceDecimal\Model\Currency as PriceDecimalCurrency;

/**
 * Plugin for Lillik\PriceDecimal\Model\Currency.
 *
 * Sets the precision option when converting currency amounts to string.
 */
class Currency extends PriceFormatPluginAbstract
{
    /**
     * Before plugin for toCurrency to inject configured precision.
     *
     * @param PriceDecimalCurrency $subject
     * @param mixed ...$arguments
     * @return array
     */
    public function beforeToCurrency(
        PriceDecimalCurrency $subject,
        ...$arguments
    ): array {
        if ($this->getConfig()->isEnable()) {
            $arguments[1]['precision'] = $subject->getPricePrecision();
        }

        return $arguments;
    }
}
