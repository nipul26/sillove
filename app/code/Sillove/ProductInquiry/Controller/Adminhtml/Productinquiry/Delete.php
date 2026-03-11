<?php

namespace Sillove\Productinquiry\Controller\Adminhtml\Productinquiry;

use Magento\Backend\App\Action\Context;
use Sillove\ProductInquiry\Model\ProductinquiryFactory;
use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * @var ProductinquiryFactory
     */
    protected $_productInquiryFactory;

    /**
     * Delete Constructor
     *
     * @param Context $context
     * @param ProductinquiryFactory $productInquiryFactory
     */
    public function __construct(
        Context $context,
        ProductinquiryFactory $productInquiryFactory
    ) {
        parent::__construct($context);
        $this->_productInquiryFactory = $productInquiryFactory;
    }

    /**
     * Execute Method
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->_redirect('productinquiry/productinquiry/index');
            return;
        }
        try {
            $rowData = $this->_productInquiryFactory->create();
            $rowData->load($id);
            $rowData->delete();
            $this->messageManager->addSuccess(__('Row data has been successfully deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('productinquiry/productinquiry/index');
    }
}
