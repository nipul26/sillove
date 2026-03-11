<?php

namespace Sillove\ProductInquiry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ShowProductType implements ArrayInterface
{
    /**
     * Products Options
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('All Products')],
            ['value' => '0', 'label' => __('Specific Product')],
        ];
    }
}
