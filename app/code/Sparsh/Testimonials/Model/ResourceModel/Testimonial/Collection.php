<?php


namespace Sparsh\Testimonials\Model\ResourceModel\Testimonial;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Initialise model and resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\Testimonials\Model\Data::class,
            \Sparsh\Testimonials\Model\ResourceModel\Testimonial::class
        );
    }
  
    /**
     * GetIdFieldName
     *
     * @return void
     */
    public function getIdFieldName()
    {
        return 'testimonial_id';
    }
}
