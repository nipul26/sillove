<?php

namespace Sillove\Productlabels\Controller\Adminhtml\Index;

use Sillove\Productlabels\Model\ProductLabelFactory;
use Sillove\Productlabels\Model\ResourceModel\ProductLabel\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ProductLabelFactory
     */
    protected $productlabelFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ProductLabelFactory $productlabelFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductLabelFactory $productlabelFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productlabelFactory = $productlabelFactory;
        parent::__construct($context);
    }

    /**
     * Execute the controller action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $lable_data = $this->collectionFactory->create();
        foreach ($lable_data as $value) {
            $templateId[] = $value['label_id'];
        }
        $parameterData = $this->getRequest()->getParams('label_id');
        $selectedAppsid = $this->getRequest()->getParams('label_id');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $templateId;
            } else {
                $selectedAppsid = array_diff($templateId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('label_id', ['in' => $selectedAppsid]);
        $delete = 0;
        $model = [];
        foreach ($collection as $item) {
            $this->deleteById($item->getId());
            $delete++;
        }
        $this->messageManager->addSuccess(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete a record by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteById($id)
    {
        $item = $this->productlabelFactory->create();
        $item->load($id);
        $item->delete();
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
