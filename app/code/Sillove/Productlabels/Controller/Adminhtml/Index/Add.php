<?php

namespace Sillove\Productlabels\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Sillove\Productlabels\Model\ProductLabelFactory;

class Add extends Action
{
    /**
     * @var ProductLabelFactory
     */
    protected $productLabelFactory;
    
    /**
     * @var ResultFactory
     */
    protected $resultPageFactory;

    /**
     * @var PageFactory
     */
    protected $resultFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductLabelFactory $productLabelFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductLabelFactory $productLabelFactory,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->productLabelFactory = $productLabelFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('label_id');

        try {
            $productLabelModel = $this->productLabelFactory->create();
            $productLabelModel->load($id);

            if ($id && !$productLabelModel->getId()) {

                $this->messageManager->addErrorMessage(__('The specified Product Label ID does not exist.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('*/*/index');

                return $resultRedirect;
            }

            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set($id ? __('Edit Label Rule') : __('Add New Label Rule'));

            return $resultPage;
        } catch (NoSuchEntityException $e) {

            $this->messageManager->addErrorMessage(__('An error occurred while processing the request.'));
        }
    }
    
    /**
     * Check if action is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sillove_Productlabels::menu');
    }
}
