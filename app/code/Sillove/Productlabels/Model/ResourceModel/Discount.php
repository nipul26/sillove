<?php

namespace Sillove\Productlabels\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Discount extends AbstractDb
{
    /**
     * Initialize the model resource and define the main table and primary key.
     */
    protected function _construct()
    {
        $this->_init('sillove_product_labels_discount', 'discount_id');
    }
}
