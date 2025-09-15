<?php

namespace Sillove\ProductSorting\Plugin;

use Magento\Catalog\Block\Product\ProductList\Toolbar as ToolbarBlock;

class Toolbar
{
    public function aroundSetCollection(ToolbarBlock $subject, \Closure $proceed, $collection)
    {
        $currentOrder = $subject->getCurrentOrder();

        if ($currentOrder) {
            if ($currentOrder == "name_desc") {
                $collection->setOrder('name', 'DESC');
            } elseif ($currentOrder === 'name') {
                $collection->setOrder('name', 'ASC');
            }
            return $proceed($collection);
        }
    }
}
