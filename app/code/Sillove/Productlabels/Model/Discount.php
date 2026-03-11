<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Model\AbstractModel;
use Sillove\Productlabels\Model\ResourceModel\Discount as DiscountResourceModel;

class Discount extends AbstractModel
{
    /**
     * Initialize the discount resource model instance.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(DiscountResourceModel::class);
    }
}
