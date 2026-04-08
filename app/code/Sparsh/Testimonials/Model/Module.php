<?php

namespace Sparsh\Testimonials\Model;

use Magento\Framework\Model\AbstractModel;

class Module extends AbstractModel
{

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Sparsh\Testimonials\Model\ResourceModel\Module::class);
    }
}
