<?php

namespace Sillove\ProductSorting\Plugin;

use Magento\Catalog\Model\Config as CatalogConfig;

class AddSorting
{
    /**
    * Append A-Z and Z-A label options to the sort-by dropdown.
    *
    * @param CatalogConfig $subject
    * @param array $result [code => label]
    * @return array
    */
    public function afterGetAttributeUsedForSortByArray(CatalogConfig $subject, array $result): array
    {
        // Ensure standard name ascending label is present/overridden nicely
        $result['name'] = __('Product Name A to Z');

        // Add our custom key for descending by name
        $result['name_desc'] = __('Product Name Z to A');

        return $result;
    }
}
