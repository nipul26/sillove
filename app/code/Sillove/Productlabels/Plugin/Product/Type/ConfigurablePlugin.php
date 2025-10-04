<?php

namespace Sillove\Productlabels\Plugin\Product\Type;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurablePlugin
{
    /**
     * Plugin to add the 'discount' attribute to the collection of used products
     *
     * @param Configurable $subject
     * @param array $result
     * @return array
     */
    public function afterGetUsedProductCollection(
        Configurable $subject,
        $result
    ) {
        $result->addAttributeToSelect('discount');

        return $result;
    }
}
