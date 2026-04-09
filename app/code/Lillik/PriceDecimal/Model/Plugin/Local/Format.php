<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Model\Plugin\Local;

use Magento\Framework\Locale\FormatInterface;
use Lillik\PriceDecimal\Model\Plugin\PriceFormatPluginAbstract;

/**
 * Plugin for Magento\Framework\Locale\FormatInterface.
 *
 * Adjusts the price format precision and required precision
 * returned by getPriceFormat.
 */
class Format extends PriceFormatPluginAbstract
{
    /**
     * After plugin for getPriceFormat to override precision values.
     *
     * @param FormatInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetPriceFormat(FormatInterface $subject, array $result): array
    {
        if ($this->getConfig()->isEnable()) {
            $precision = $this->getPricePrecision();
            $result['precision'] = $precision;
            $result['requiredPrecision'] = $precision;
        }

        return $result;
    }
}
