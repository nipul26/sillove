<?php
/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\DeleteOrders\Model\ResourceModel\Archive;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * Get archive collection
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aitoc\DeleteOrders\Model\Archive::class,
            \Aitoc\DeleteOrders\Model\ResourceModel\Archive::class
        );
    }
}
