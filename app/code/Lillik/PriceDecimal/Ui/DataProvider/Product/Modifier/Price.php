<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Request\DataPersistorInterface;
use Lillik\PriceDecimal\Model\ConfigInterface;
use Lillik\PriceDecimal\Model\PricePrecisionConfigTrait;

/**
 * Product form data modifier for price precision.
 *
 * Formats the product price in the admin form to match
 * the configured decimal precision.
 */
class Price extends AbstractModifier
{
    use PricePrecisionConfigTrait;

    /**
     * @var LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $moduleConfig;

    /**
     * @param LocatorInterface $locator
     * @param DataPersistorInterface $dataPersistor
     * @param ConfigInterface $moduleConfig
     */
    public function __construct(
        LocatorInterface $locator,
        DataPersistorInterface $dataPersistor,
        ConfigInterface $moduleConfig
    ) {
        $this->locator = $locator;
        $this->dataPersistor = $dataPersistor;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Modify product form data to apply price precision formatting.
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        if ($this->moduleConfig->isEnable()) {
            if (!$this->locator->getProduct()->getId()
                && $this->dataPersistor->get('catalog_product')
            ) {
                return $this->resolvePersistentData($data);
            }
            $productId = $this->locator->getProduct()->getId();
            $productPrice = $this->locator->getProduct()->getPrice();
            $data[$productId][self::DATA_SOURCE_DEFAULT]['price'] = $this->formatPrice($productPrice);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }

    /**
     * Format price value with configured precision.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatPrice($value): string
    {
        return $value !== null
            ? number_format((float) $value, $this->getPricePrecision(), '.', '')
            : '';
    }

    /**
     * Resolve data from data persistor for unsaved products.
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data): array
    {
        $persistentData = (array) $this->dataPersistor->get('catalog_product');
        $this->dataPersistor->clear('catalog_product');
        $productId = $this->locator->getProduct()->getId();

        if (empty($data[$productId][self::DATA_SOURCE_DEFAULT])) {
            $data[$productId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$productId] = array_replace_recursive(
            $data[$productId][self::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }
}
