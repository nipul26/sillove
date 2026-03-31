<?php
namespace Codezspark\CouponList\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CouponstoDisplay implements ArrayInterface
{
    /**
     * Convert options array to option array format.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'all', 'label' => ('All')],
            ['value' => 'valid', 'label' => ('Valid Only')]
        ];
    }
}
