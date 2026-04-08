<?php
 
namespace Sparsh\Testimonials\Controller\Adminhtml\Testimonials;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
 
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sparsh_Testimonials::testimonials_manage');
        $resultPage->addBreadcrumb(__('Manage'), __('Manage'));
        $resultPage->addBreadcrumb(__('Testimonials'), __('Testimonials'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Testimonials'));
        return $resultPage;
    }
}
