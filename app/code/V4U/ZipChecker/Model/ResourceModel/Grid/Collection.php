<?php

namespace V4U\ZipChecker\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'V4U\ZipChecker\Model\Grid',
            'V4U\ZipChecker\Model\ResourceModel\Grid'
        );
    }
}
