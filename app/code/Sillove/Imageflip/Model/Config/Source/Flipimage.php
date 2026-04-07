<?php

namespace Sillove\Imageflip\Model\Config\Source;

class Flipimage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * [toOptionArray description]
     *
     * @return [array] Return array value of image configuration
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'y',
                'label' => __('Horizontal'),
            ],
            [
                'value' => 'x',
                'label' => __('Vertical'),
            ],
            [
                'value' => 'fade',
                'label' => __('Fade'),
            ],
            [
                'value' => 'none',
                'label' => __('None'),
            ],
        ];
    }
}
