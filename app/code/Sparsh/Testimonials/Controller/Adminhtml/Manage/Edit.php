<?php
namespace Sparsh\Testimonials\Controller\Adminhtml\Manage;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * ResultPageFactory instance
     *
     * @var mixed
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
   
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        $resultPage=$this->resultPageFactory->create();
       
        $resultPage->getConfig()->getTitle()
            ->prepend($id ? __('Edit Testimonial') : __('Add New Testimonial'));
        $resultPage->setActiveMenu('Sparsh_Testimonials::testimonials_manage');
        return $resultPage;
    }
}
