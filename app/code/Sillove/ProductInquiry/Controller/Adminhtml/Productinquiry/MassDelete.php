<?php

namespace Sillove\Productinquiry\Controller\Adminhtml\Productinquiry;

use Sillove\ProductInquiry\Model\Productinquiry;
use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Productinquiry
     */
    protected $ProductInquiryModel;

    /**
     * Mass Delete Constructor
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Productinquiry $ProductInquiryModel
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Productinquiry $ProductInquiryModel,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->_filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->ProductInquiryModel = $ProductInquiryModel;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $ProductInquiryData = $this->collectionFactory->create();

        foreach ($ProductInquiryData as $value) {
            $templateId[] = $value['id'];
        }

        $parameterData = $this->getRequest()->getParams('id');
        $selectedAppsId = $this->getRequest()->getParams('id');

        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsId = $parameterData['selected'];
        }

        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsId = $templateId;
            } else {
                $selectedAppsId = array_diff($templateId, $parameterData['excluded']);
            }
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in' => $selectedAppsId]);
        $delete = 0;
        $model = [];

        foreach ($collection as $item) {
            $this->deleteById($item->getId());
            $delete++;
        }

        $this->messageManager->addSuccess(__('A total of %1 records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Delete By Id
     *
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $item = $this->ProductInquiryModel->load($id);
        $item->delete();
    }
}
