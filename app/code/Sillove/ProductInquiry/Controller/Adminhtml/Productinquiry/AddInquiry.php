<?php

namespace Sillove\Productinquiry\Controller\Adminhtml\Productinquiry;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class AddInquiry extends Action
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Add Inquiry Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Add Inquiry'));
        return $resultPage;
    }
}
