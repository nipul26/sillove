<?php
namespace Sillove\Testimonial\Controller\Adminhtml;

abstract class Index extends \Sillove\Testimonial\Controller\Adminhtml\Testimonial
{
    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sillove_Testimonial::testimonial');
    }
}
