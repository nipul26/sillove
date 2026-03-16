<?php
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

class NewAction extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
     /**
      * @inheritdoc
      */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
