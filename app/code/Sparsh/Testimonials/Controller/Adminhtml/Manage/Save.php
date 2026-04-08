<?php

namespace Sparsh\Testimonials\Controller\Adminhtml\Manage;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * ResultPageFactory instance
     *
     * @var bool
     */
    protected $resultPageFactory = false;
    
    /**
     * TestimonialLoader instance
     *
     * @var mixed
     */
    protected $testimonialLoader;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Sparsh\Testimonials\Model\Data $testimonialLoader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Sparsh\Testimonials\Model\Data $testimonialLoader
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->testimonialLoader = $testimonialLoader;
    }
  
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam("testimonial_id");
        $message = $this->getRequest()->getParam("message");
        $name = $this->getRequest()->getParam("name");
        $rating = (int)$this->getRequest()->getParam("rating");
        $testimonial_date = $this->getRequest()->getParam("testimonial_date");
        $status = $this->getRequest()->getParam("status");
        $show_on_home = $this->getRequest()->getParam("show_on_home");
        $sortOrder = $this->getRequest()->getParam("sort_order");
        $item = $this->testimonialLoader;
        if ($id != null) {
            $item = $this->testimonialLoader->load($id);
            $item->setMessage($message);
            $item->setName($name);
            $item->setRating($rating);
            $item->setTestimonial_date($testimonial_date);
            $item->setStatus($status);
            $item->setShowOnHome($show_on_home);
            $item->setSortOrder($sortOrder);
            $item->save();
            $this->messageManager->addSuccess(
                __('Record updated successfully.')
            );
        } else {
            $item->setMessage($message);
            $item->setName($name);
            $item->setRating($rating);
            $item->setTestimonial_date($testimonial_date);
            $item->setStatus($status);
            $item->setShowOnHome($show_on_home);
            $item->setSortOrder($sortOrder);
            $item->save();
            $this->messageManager->addSuccess(
                __('Record added successfully.')
            );
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect->setPath('*/testimonials/index');
    }
}
