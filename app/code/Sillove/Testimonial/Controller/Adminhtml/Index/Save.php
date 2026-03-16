<?php
// @codingStandardsIgnoreFile
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->testimonialFactory->create();
            $storeViewId = $this->getRequest()->getParam('store');

            if ($id = $this->getRequest()->getParam('testimonial_id')) {
                $model->load($id);
            }
            if (isset($data['stores'])) {
                $data['stores'] = implode(',', $data['stores']);
            }
            $model->setData($data)
                ->setStoreViewId($storeViewId);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The Testimonial menu has been saved.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'testimonial_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_testimonial_id' => $this->getRequest()->getParam('current_testimonial_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
