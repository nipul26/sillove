<?php

namespace Sillove\Productlabels\Controller\Adminhtml\Index;

use Sillove\Productlabels\Model\ProductLabelFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action
{
    /**
     * @var ProductLabelFactory
     */
    protected $productLabelmodel;

    /**
     * @param Context $context
     * @param ProductLabelFactory $productLabelmodel
     */
    public function __construct(
        Context $context,
        ProductLabelFactory $productLabelmodel
    ) {
        parent::__construct($context);
        $this->productLabelmodel = $productLabelmodel;
    }

    /**
     * Execute action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('label_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->productLabelmodel->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The label rule is successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['label_id' => $id]);
            }
        }
        $this->messageManager->addError(__('The label rule does not exist'));
        return $resultRedirect->setPath('*/*/');
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
