<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model\Plugin;

use Magento\Directory\Model\PriceCurrency as MagentoPriceCurrency;

/**
 * Plugin for Magento\Directory\Model\PriceCurrency.
 *
 * Adjusts price formatting, rounding, and conversion precision
 * based on module configuration.
 */
class PriceCurrency extends PriceFormatPluginAbstract
{
    /**
     * Before plugin for format method to set price precision.
     *
     * @param MagentoPriceCurrency $subject
     * @param mixed ...$args
     * @return array
     */
    public function beforeFormat(
        MagentoPriceCurrency $subject,
        ...$args
    ): array {
        if ($this->getConfig()->isEnable()) {
            if (!isset($args[1])) {
                $args[1] = true;
            }
            $args[2] = $this->getPricePrecision();
        }

        return $args;
    }

    /**
     * Around plugin for round method to apply custom precision.
     *
     * @param MagentoPriceCurrency $subject
     * @param callable $proceed
     * @param float $price
     * @param mixed ...$args
     * @return float
     */
    public function aroundRound(
        MagentoPriceCurrency $subject,
        callable $proceed,
        $price,
        ...$args
    ): float {
        if ($this->getConfig()->isEnable()) {
            return round((float) $price, $this->getPricePrecision());
        }

        return $proceed($price);
    }

    /**
     * Before plugin for convertAndFormat method to set precision.
     *
     * @param MagentoPriceCurrency $subject
     * @param mixed ...$args
     * @return array
     */
    public function beforeConvertAndFormat(
        MagentoPriceCurrency $subject,
        ...$args
    ): array {
        if ($this->getConfig()->isEnable()) {
            $args[1] = $args[1] ?? null;
            $args[2] = $this->getPricePrecision();
        }

        return $args;
    }

    /**
     * Before plugin for convertAndRound method to set precision.
     *
     * @param MagentoPriceCurrency $subject
     * @param mixed ...$args
     * @return array
     */
    public function beforeConvertAndRound(
        MagentoPriceCurrency $subject,
        ...$args
    ): array {
        if ($this->getConfig()->isEnable()) {
            $args[1] = $args[1] ?? null;
            $args[2] = $args[2] ?? null;
            $args[3] = $this->getPricePrecision();
        }

        return $args;
    }
}
