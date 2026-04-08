<?php
 
namespace Sparsh\Testimonials\Controller\Adminhtml\Manage;

class Addnew extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * Addnew constructor.
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
        $resultPage->getConfig()->getTitle()
            ->set(__('Add New Testimonial'));
        $resultPage->setActiveMenu('Sparsh_Testimonials::testimonials_manage');
        return $resultPage;
    }
}
