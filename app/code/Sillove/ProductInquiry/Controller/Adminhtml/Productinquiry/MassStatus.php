<?php

namespace Sillove\ProductInquiry\Controller\Adminhtml\Productinquiry;

use Sillove\ProductInquiry\Model\Productinquiry;
use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;

class MassStatus extends Action
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
     * @var Productinquiry
     */
    protected $ProductInquiryModel;

    /**
     * Mass Status Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Productinquiry $ProductInquiryModel
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Productinquiry $ProductInquiryModel
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->ProductInquiryModel = $ProductInquiryModel;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $ProductInquiryData = $this->collectionFactory->create();

        foreach ($ProductInquiryData as $value) {
            $templateId[] = $value['id'];
        }

        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsId = $this->getRequest()->getParams('status');

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
        $status = 0;
        $model = [];

        foreach ($collection as $item) {
            $this->setStatus($item->getId(), $this->getRequest()->getParam('status'));
            $status++;
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Set Status
     *
     * @param int $id
     * @param string $Param
     * @return void
     */
    public function setStatus($id, $Param)
    {
        $item = $this->ProductInquiryModel->load($id);
        $item->setInquiryStatus($Param)->save();
    }
}
