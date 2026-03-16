<?php
namespace Sillove\Testimonial\Model\ResourceModel\Testimonial;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Sillove\Testimonial\Model\Testimonial::class,
            \Sillove\Testimonial\Model\ResourceModel\Testimonial::class
        );
    }

    /**
     * Set order by order and testimonial_id
     *
     * @return $this
     */
    public function setOrderByTestimonial()
    {
        $this->getSelect()->order(['order asc', 'testimonial_id desc']);
        return $this;
    }
}
