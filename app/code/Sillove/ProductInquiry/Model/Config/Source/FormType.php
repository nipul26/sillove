<?php

namespace Sillove\ProductInquiry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class FormType implements ArrayInterface
{
    /**
     * Form Options
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'popup', 'label' => __('Popup')],
            ['value' => 'tab', 'label' => __('Tab')],
        ];
    }
}
