<?php

namespace Sillove\Productlabels\Model\ResourceModel\Discount;

use Sillove\Productlabels\Model\Discount as DiscountModel;
use Sillove\Productlabels\Model\ResourceModel\Discount as DiscountResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize the model resource and define the model and resource model classes.
     */
    protected function _construct()
    {
        $this->_init(
            DiscountModel::class,
            DiscountResourceModel::class
        );
    }
}
