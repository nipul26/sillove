<?php

namespace Sparsh\Testimonials\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Testimonial extends AbstractDb
{
    /**
     * Initialise table and primary id
     */
    protected function _construct()
    {
        $this->_init('sparsh_testimonials', 'testimonial_id');
    }
}
