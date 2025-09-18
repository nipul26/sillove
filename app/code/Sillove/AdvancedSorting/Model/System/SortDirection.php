<?php

namespace Sillove\AdvancedSorting\Model\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SortDirection
 */
class SortDirection implements ArrayInterface
{
    const ASCENDING  = "asc";
    const DESCENDING = "desc";

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::ASCENDING  => __('Ascending'),
            self::DESCENDING => __('Descending')
        ];
    }
}
