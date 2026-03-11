<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Data\OptionSourceInterface;

class ShapeList implements OptionSourceInterface
{
    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options['0'] = __('Shape-1');
        $options['1'] = __('Shape-2');
        $options['2'] = __('Shape-3');
        $options['3'] = __('Shape-4');
        $options['4'] = __('Shape-5');
        $options['5'] = __('Shape-6');
        $options['6'] = __('Shape-7');
        $options['7'] = __('Shape-8');
        $options['8'] = __('Shape-9');
        $options['9'] = __('Shape-10');
        $options['10'] = __('Shape-11');
        return $options;
    }

    /**
     * Get all options for the dropdown
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    /**
     * Get options for the dropdown.
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * Convert options array to option array format.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
