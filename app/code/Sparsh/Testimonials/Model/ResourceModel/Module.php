<?php

namespace Sparsh\Testimonials\Model\ResourceModel;

class Module extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('sparsh_testimonials', 'testimonial_id');
    }
}
