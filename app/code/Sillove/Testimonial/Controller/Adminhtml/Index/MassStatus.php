<?php
namespace Sillove\Testimonial\Controller\Adminhtml\Index;

class MassStatus extends \Sillove\Testimonial\Controller\Adminhtml\Action
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');
        if (!is_array($testimonialIds) || empty($testimonialIds)) {
            $this->messageManager->addError(__('Please select Testimonial(s).'));
        } else {
            $collection = $this->testimonialCollectionFactory->create()
                ->addFieldToFilter('testimonial_id', ['in' => $testimonialIds]);
            try {
                foreach ($collection as $item) {
                    $item->setStoreViewId($storeViewId)
                        ->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($testimonialIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
