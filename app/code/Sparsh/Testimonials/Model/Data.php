<?php


namespace Sparsh\Testimonials\Model;

use Magento\Framework\Model\AbstractModel;

class Data extends AbstractModel
{
    /**
     *  Initialise resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\Testimonials\Model\ResourceModel\Testimonial::class
        );
    }
}
