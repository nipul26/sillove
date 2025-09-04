<?php

namespace Sillove\Catalog\Model\Source\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ProductTypeOptions extends AbstractSource
{
    protected $_options;

    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '', 'label' => __('')],
                ['value' => 'rings', 'label' => __('Rings')],
                ['value' => 'earings', 'label' => __('Earings')],
                ['value' => 'bracelet', 'label' => __('Bracelets')],
                ['value' => 'necklaces', 'label' => __('Necklaces')],
                ['value' => 'chains', 'label' => __('Chains')],
                ['value' => 'jewelry_set', 'label' => __('Jewelry Set')],
                ['value' => 'anklets', 'label' => __('Anklets')],
            ];
        }
        return $this->_options;
    }
}
