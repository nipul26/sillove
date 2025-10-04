<?php

namespace Sillove\Productlabels\Model\ResourceModel\ProductLabel;

use Sillove\Productlabels\Model\ProductLabel as ProductLabelModel;
use Sillove\Productlabels\Model\ResourceModel\ProductLabel as ProductLabelResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize the model resource and define the model and resource model classes.
     */
    protected function _construct()
    {
        $this->_init(
            ProductLabelModel::class,
            ProductLabelResourceModel::class
        );
    }
}
