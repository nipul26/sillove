<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Data\OptionSourceInterface;

class LabelPosition implements OptionSourceInterface
{
    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options['0'] = __('Top Left');
        $options['1'] = __('Top Center');
        $options['2'] = __('Top Right');
        $options['3'] = __('Center Left');
        $options['4'] = __('Center Right');
        $options['5'] = __('Bottom Left');
        $options['6'] = __('Bottom Center');
        $options['7'] = __('Bottom Right');
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
