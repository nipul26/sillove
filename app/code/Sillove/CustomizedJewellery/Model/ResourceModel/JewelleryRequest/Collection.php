<?php
namespace Sillove\CustomizedJewellery\Model\ResourceModel\JewelleryRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Sillove\CustomizedJewellery\Model\JewelleryRequest::class,
            \Sillove\CustomizedJewellery\Model\ResourceModel\JewelleryRequest::class
        );
    }
}
