<?php

namespace Sillove\Productlabels\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DisplayMode implements OptionSourceInterface
{
    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options[0] = 'Always';
        $options[1] = 'Hover';
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
