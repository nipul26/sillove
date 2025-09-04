<?php

namespace Sillove\ProductInquiry\Controller\Adminhtml\Productinquiry;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Sillove\ProductInquiry\Model\ProductinquiryFactory;
use Sillove\ProductInquiry\Helper\ProductInquiryHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class Save extends Action
{
    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductFactory
     */
    protected $product;
    /**
     * @var ProductinquiryFactory
     */
    protected $_productInquiryFactory;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var ProductInquiryHelper
     */
    protected $_ProductInquiryHelper;

    /**
     * Save Constructor
     *
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $product
     * @param ProductinquiryFactory $productInquiryFactory
     * @param DataPersistorInterface $dataPersistor
     * @param ProductInquiryHelper $ProductInquiryHelper
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ProductFactory $product,
        ProductinquiryFactory $productInquiryFactory,
        DataPersistorInterface $dataPersistor,
        ProductInquiryHelper $ProductInquiryHelper
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->product = $product;
        $this->_productInquiryFactory = $productInquiryFactory;
        $this->dataPersistor = $dataPersistor;
        $this->_ProductInquiryHelper = $ProductInquiryHelper;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $productCollection = $this->product->create()->load($data['prd_entity_id']);
        $data['prd_sku'] = $productCollection->getSku();
        $this->dataPersistor->set('inquiry_data', $data);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $update_datetime = date("Y-m-d h:i:s");
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            $this->messageManager->addError(__("Please fill the all require details "));
            return $resultRedirect->setPath('productinquiry/productinquiry/addinquiry');
        }

        if (isset($data['attechment_file'][0]['name'])) {
            $data['attechment_file'] = $data['attechment_file'][0]['name'];
        } else {
            $data['attechment_file'] = '';
        }

        try {
            $rowData = $this->_productInquiryFactory->create();
            $rowData->setData($data);

            if (isset($data['id'])) {
                $rowData->setId($data['id']);
                $rowData->setUpadteDatetime($update_datetime);
            }

            $rowData->save();
            $this->dataPersistor->clear('inquiry_data');
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));

            if ($this->getRequest()->getParam('back')) {
                if ($this->getRequest()->getParam('back') == 'add') {
                    return $resultRedirect->setPath('productinquiry/productinquiry/addinquiry');
                } elseif ($this->getRequest()->getParam('back') == 'email') {
                    $this->_ProductInquiryHelper->sendEmail(0, $rowData);
                    return $resultRedirect->setPath('productinquiry/productinquiry/index');
                } else {
                    return $resultRedirect->setPath(
                        'productinquiry/productinquiry/editinquiry',
                        [
                            'id' => $rowData->getId(),
                            '_current' => true,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setPath('productinquiry/productinquiry/addinquiry');
        }
        return $resultRedirect->setPath('productinquiry/productinquiry/index');
    }
}
