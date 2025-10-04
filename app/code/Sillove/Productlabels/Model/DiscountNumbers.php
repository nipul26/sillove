<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountNumbers implements OptionSourceInterface
{
    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options['0'] = __('1% - 10%');
        $options['1'] = __('11% - 20%');
        $options['2'] = __('21% - 30%');
        $options['3'] = __('31% - 40%');
        $options['4'] = __('41% - 50%');
        $options['5'] = __('51% - 60%');
        $options['6'] = __('61% - 70%');
        $options['7'] = __('71% - 80%');
        $options['8'] = __('81% - 90%');
        $options['9'] = __('91% - 100%');
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
