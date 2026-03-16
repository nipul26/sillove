<?php
namespace Sillove\Testimonial\Block\Adminhtml;

/**
 * @api
 * @since 100.0.2
 */
class Testimonial extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testimonial';
        $this->_blockGroup = 'Sillove_Testimonial';
        $this->_headerText = __('Testimonial');
        $this->_addButtonLabel = __('Add New Testimonial');
        parent::_construct();
    }
}
