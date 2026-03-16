<?php
namespace Sillove\Testimonial\Model\ResourceModel;

class Testimonial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('sillove_testimonial', 'testimonial_id');
    }
}
