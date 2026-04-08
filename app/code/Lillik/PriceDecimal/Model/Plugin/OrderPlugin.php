<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model\Plugin;

use Magento\Sales\Model\Order;

/**
 * Plugin for Magento\Sales\Model\Order.
 *
 * Adjusts price precision for order price formatting.
 */
class OrderPlugin extends PriceFormatPluginAbstract
{
    /**
     * Before plugin for formatPricePrecision to apply configured precision.
     *
     * @param Order $subject
     * @param mixed ...$args
     * @return array
     */
    public function beforeFormatPricePrecision(
        Order $subject,
        ...$args
    ): array {
        if ($this->getConfig()->isEnable()) {
            $args[1] = $this->getPricePrecision();
        }

        return $args;
    }
}
