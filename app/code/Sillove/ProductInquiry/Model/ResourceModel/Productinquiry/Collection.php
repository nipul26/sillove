<?php

namespace Sillove\ProductInquiry\Model\ResourceModel\Productinquiry;

use Sillove\ProductInquiry\Model\Productinquiry as ProductinquiryModel;
use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry as ProductinquiryResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Collection Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            ProductinquiryModel::class,
            ProductinquiryResourceModel::class
        );
    }
}
