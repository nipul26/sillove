<?php

namespace Sparsh\Testimonials\Model\Category\Attribute\Source;

/**
 * Class Boolean
 * For use Yes or No option
 */
class Boolean implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Yes', 'label' => 'Yes'],
            ['value' => 'No', 'label' => 'No']
        ];
    }
}
