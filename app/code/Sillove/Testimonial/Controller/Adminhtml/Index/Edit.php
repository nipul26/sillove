<?php
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

class Edit extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->testimonialFactory->create();

        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Testimonial no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('testimonial', $model);
        return $this->resultPageFactory->create();
    }
}
