<?php

namespace Sillove\ProductInquiry\Model;

use Magento\Framework\Data\OptionSourceInterface;

class AllowAttachment implements OptionSourceInterface
{
    /**
     * Get Options Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = ['0' => __('No'), '1' => __('Yes')];
        return $options;
    }

    /**
     * Get All Options
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
     * Get Options
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
     * Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
