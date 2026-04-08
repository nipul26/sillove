<?php

namespace Sparsh\Testimonials\Model\ResourceModel\Module;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\Testimonials\Model\Module::class,
            \Sparsh\Testimonials\Model\ResourceModel\Module::class
        );
    }
}
