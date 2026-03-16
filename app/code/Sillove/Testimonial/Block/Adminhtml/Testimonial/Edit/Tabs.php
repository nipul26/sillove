<?php
namespace Sillove\Testimonial\Block\Adminhtml\Testimonial\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId('edit_form');
    }
}
