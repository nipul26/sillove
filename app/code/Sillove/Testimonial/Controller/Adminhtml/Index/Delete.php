<?php
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

class Delete extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        try {
            $item = $this->testimonialFactory->create()->setId($id);
            $item->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
