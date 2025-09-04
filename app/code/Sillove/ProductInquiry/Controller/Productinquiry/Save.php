<?php

namespace Sillove\ProductInquiry\Controller\Productinquiry;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Sillove\ProductInquiry\Model\ProductinquiryFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ProductFactory;
use Sillove\ProductInquiry\Model\Productinquiry;
use Magento\Framework\Filesystem\Io\File;
use Sillove\ProductInquiry\Helper\ProductInquiryHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Action\Action;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;
    /**
     * @var Http
     */
    protected $_request;
    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;
    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var ProductFactory
     */
    protected $product;
    /**
     * @var ProductinquiryFactory
     */
    protected $_productInquiryFactory;
    /**
     * @var ProductInquiryHelper
     */
    protected $_productInquiryHelper;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Productinquiry
     */
    private $productInquiryCollection;
    /**
     * @var File
     */
    private $file;
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * Save Constructor
     *
     * @param Context $context
     * @param Http $request
     * @param PageFactory $pageFactory
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     * @param ProductFactory $product
     * @param Productinquiry $productInquiryCollection
     * @param File $file
     * @param ProductinquiryFactory $productInquiryFactory
     * @param ProductInquiryHelper $productInquiryHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Validator $validator
     */
    public function __construct(
        Context $context,
        Http $request,
        PageFactory $pageFactory,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        ProductFactory $product,
        Productinquiry $productInquiryCollection,
        File $file,
        ProductinquiryFactory $productInquiryFactory,
        ProductInquiryHelper $productInquiryHelper,
        DataPersistorInterface $dataPersistor,
        Validator $validator
    ) {
        parent::__construct($context);
        $this->_request = $request;
        $this->_pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->product = $product;
        $this->productInquiryCollection = $productInquiryCollection;
        $this->file = $file;
        $this->_productInquiryFactory = $productInquiryFactory;
        $this->_productInquiryHelper = $productInquiryHelper;
        $this->dataPersistor = $dataPersistor;
        $this->validator = $validator;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->validator->validate($this->getRequest())) {

            $fileUp = $this->getRequest()->getFiles('attechment_file');
            $fileUpload = 0;
            $data = $this->getRequest()->getPostValue();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            if (!$data) {
                $this->messageManager->addError(__('Product Inquiry Details Not Found'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            $showMsg = $this->_productInquiryHelper->getSuccessMassage();
            if ($showMsg == '') {
                $msg = 'Product Inquiry has been successfully saved.';
            } else {
                $msg = $showMsg;
            }

            $allowAttachStatus = $this->_productInquiryHelper->getFileAttachmentStatus();
            if (($allowAttachStatus == 1) && (!empty($fileUp)) && ($fileUp['name'])) {
                try {
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'attechment_file']);
                    $attachmentExtension = $this->_productInquiryHelper->getFileAttachmentExtension();
                    $attachmentExtension = explode(',', $attachmentExtension);
                    $formAttachmentExtension = [];
                    foreach ($attachmentExtension as $validExtension) {
                        $validExtension = preg_replace('/\s+/', '', $validExtension);
                        $formAttachmentExtension[] = $validExtension;
                    }

                    $uploaderFactory->setAllowedExtensions($formAttachmentExtension);
                    $imageAdapter = $this->adapterFactory->create();
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('Product_Inquiry_Attachments');
                    $result = $uploaderFactory->save($destinationPath);

                    if (!$result) {
                        throw new LocalizedException(
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
                    $data['attechment_file'] = $result['file'];
                    $fileUpload = 1;
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Attachment not Uploaded, Please try Again'));
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            }

            $reCaptcha = $this->_productInquiryHelper->getReCaptcha();
            if ($reCaptcha) {
                try {
                    $this->dataPersistor->set('inquiry_form_data', $data);
                    $rowData = $this->_productInquiryFactory->create();
                    $rowData->setData($data);
                    $rowData->save();
                    $this->messageManager->addSuccess(__($msg));
                    $this->_productInquiryHelper->sendEmail(1, $rowData);
                    $this->_productInquiryHelper->sendEmail(0, $rowData);
                    $this->dataPersistor->clear('inquiry_form_data');
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Please try again form was not submitted.'));
                }

                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } else {
                try {
                    $rowData = $this->_productInquiryFactory->create();
                    $rowData->setData($data);
                    $rowData->save();
                    $this->messageManager->addSuccess(__($msg));
                    $this->_productInquiryHelper->sendEmail(1, $rowData);
                    $this->_productInquiryHelper->sendEmail(0, $rowData);
                    $this->dataPersistor->clear('inquiry_form_data');
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Please try again, Form was not submitted.'));
                }

                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addError(__('Invalid form key.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
