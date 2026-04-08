<?php
namespace Sparsh\Testimonials\Controller\Adminhtml\Manage;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * TestimonialLoader instance
     *
     * @var mixed
     */
    protected $testimonialLoader;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Sparsh\Testimonials\Model\Data $testimonialLoader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Sparsh\Testimonials\Model\Data $testimonialLoader
    ) {
        parent::__construct($context);
        $this->testimonialLoader = $testimonialLoader;
    }
    
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->testimonialLoader;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The testimonial has been deleted.'));
                return $resultRedirect->setPath('*/testimonials/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/manage/edit', ['testimonial_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a testimonial to delete.'));
        return $resultRedirect->setPath('*/testimonials/');
    }
}
