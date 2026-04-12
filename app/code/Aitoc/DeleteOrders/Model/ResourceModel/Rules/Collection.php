<?php
/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\DeleteOrders\Model\ResourceModel\Rules;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * Rules collection
 */
class Collection extends AbstractCollection
{
    /**
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
            \Aitoc\DeleteOrders\Model\Rules::class,
            \Aitoc\DeleteOrders\Model\ResourceModel\Rules::class
        );
    }
}
