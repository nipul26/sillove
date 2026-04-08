<?php

namespace Sparsh\Testimonials\Model\Category\Attribute\Source;

/**
 * Class Category
 * for use status enable or disable
 */
class Category implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * ToOptionArray
     *
     * @return void
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Enabled', 'label' => 'Enabled'],
            ['value' => 'Disabled', 'label' => 'Disabled']
        ];
    }
}
