<?php
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

class Grid extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        return $this->resultLayoutFactory->create();
    }
}
