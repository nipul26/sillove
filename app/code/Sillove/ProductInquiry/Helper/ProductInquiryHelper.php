<?php

namespace Sillove\ProductInquiry\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Filesystem\Io\File;

class ProductInquiryHelper extends AbstractHelper
{
    public const ENABLE_MODULE = 'productinquiry/general_option/enable_module';
    public const FORM_TITLE = 'productinquiry/general_option/form_title';
    public const ENABLE_ALL_PRODUCT = 'productinquiry/general_option/enable_all_prd';
    public const FORM_TYPE = 'productinquiry/general_option/form_type';
    public const TAB_BUTTON_TITLE = 'productinquiry/general_option/tab_button_title';
    public const ALLOW_GUEST = 'productinquiry/general_option/allow_guest';
    public const ALLOW_ATTACH = 'productinquiry/general_option/allow_attach';
    public const ALLOW_ATTACH_EXTENSION = 'productinquiry/general_option/allow_attach_extension';
    public const THANKS_MESSAGE = 'productinquiry/general_option/thanks_msg';
    public const RE_CAPTCHA_ENABLE = 'productinquiry/captcha_setting/re_captcha_enable';
    public const EMAIL_TEMPLATE = 'productinquiry/email_setting/email_template';
    public const ADMIN_EMAIL_ADDRESS_COPY = 'productinquiry/email_setting/email_copy';
    public const EMAIL_TEMPLATE_CUSTOMER = 'productinquiry/email_setting/email_template_customer';
    public const STORE_EMAIL_LIST = 'productinquiry/email_setting/store_email_list';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductFactory
     */
    protected $product;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var File
     */
    private $file;

    /**
     * Helper Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $product
     * @param ManagerInterface $messageManager
     * @param TransportBuilder $transportBuilder
     * @param File $file
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ProductFactory $product,
        ManagerInterface $messageManager,
        TransportBuilder $transportBuilder,
        File $file
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->product = $product;
        $this->messageManager = $messageManager;
        $this->_transportBuilder = $transportBuilder;
        $this->file = $file;
    }

    /**
     * Get Module Status
     *
     * @return mixed
     */
    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(self::ENABLE_MODULE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Form Title
     *
     * @return mixed
     */
    public function getFormTitle()
    {
        return $this->scopeConfig->getValue(self::FORM_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Show All Product
     *
     * @return mixed
     */
    public function showAllProdcut()
    {
        return $this->scopeConfig->getValue(self::ENABLE_ALL_PRODUCT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Button Tab Option
     *
     * @return mixed
     */
    public function getButtonTabOption()
    {
        return $this->scopeConfig->getValue(self::FORM_TYPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Button Tab Title
     *
     * @return mixed
     */
    public function getButtonTabTitle()
    {
        return $this->scopeConfig->getValue(self::TAB_BUTTON_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Guest User Status
     *
     * @return mixed
     */
    public function getGuestUserStatus()
    {
        return $this->scopeConfig->getValue(self::ALLOW_GUEST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get File Attachment Status
     *
     * @return mixed
     */
    public function getFileAttachmentStatus()
    {
        return $this->scopeConfig->getValue(self::ALLOW_ATTACH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get File Attachment Extension
     *
     * @return mixed
     */
    public function getFileAttachmentExtension()
    {
        return $this->scopeConfig->getValue(self::ALLOW_ATTACH_EXTENSION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Success Message
     *
     * @return mixed
     */
    public function getSuccessMassage()
    {
        return $this->scopeConfig->getValue(self::THANKS_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Captcha & Re-Captcha Status
     *
     * @return mixed
     */
    public function getReCaptcha()
    {
        return $this->scopeConfig->getValue(self::RE_CAPTCHA_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Email Template
     *
     * @return mixed
     */
    public function getEmailTemplate()
    {
        return $this->scopeConfig->getValue(self::EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Admin Email
     *
     * @return mixed
     */
    public function getAdminEmailAddress()
    {
        return $this->scopeConfig->getValue(self::ADMIN_EMAIL_ADDRESS_COPY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Email Template Customer
     *
     * @return mixed
     */
    public function getEmailTemplateCustomer()
    {
        return $this->scopeConfig->getValue(self::EMAIL_TEMPLATE_CUSTOMER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Store Email Address
     *
     * @return mixed
     */
    public function getStoreEmailAddress()
    {
        return $this->scopeConfig->getValue(self::STORE_EMAIL_LIST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Store Email
     *
     * @return array
     */
    public function getStoreEmail()
    {
        $storeEmailName = $this->getStoreEmailAddress();
        $config = 'trans_email/ident_' . $storeEmailName . '/name';
        $name = $this->scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
        $config = 'trans_email/ident_' . $storeEmailName . '/email';
        $email = $this->scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
        $supportTeam = [];
        $supportTeam['name'] = $name;
        $supportTeam['email'] = $email;
        return $supportTeam;
    }

    /**
     * Send Email
     *
     * @param string $admin_customer
     * @param int $dataID
     * @return void
     * @throws NoSuchEntityException
     */
    public function sendEmail($admin_customer, $dataID)
    {
        $store = $this->_storeManager->getStore()->getId();
        $templateVar = [];
        $templateVar = $this->getEmailTemplateVariable($dataID);
        $adminTemplate = $this->getEmailTemplate();
        $adminEmailAdress = $this->getAdminEmailAddress();
        if ($admin_customer == 0) {
            $adminEmailAdress = $dataID->getEmail();
            $adminTemplate = $this->getEmailTemplateCustomer();
            $templateVar['add'] = 'yes';
        }
        if ($dataID->getInqReply() != '') {
            $templateVar['inq_reply'] = $dataID->getInqReply();
            unset($templateVar['add']);
        }
        $supportTeam = $this->getStoreEmail();
        $templateVar['support_email'] = $supportTeam['email'];
        $templateVar['support_name'] = $supportTeam['name'];
        $sender = $this->getStoreEmailAddress();

        if ($adminEmailAdress == '') {
            $this->messageManager->addErrorMessage(__('Admin email address not found.'));
        }

        $emailCount = 0;
        $adminEmailAdress = explode(',', $adminEmailAdress);
        foreach ($adminEmailAdress as $validEmail) {
            $validEmail = preg_replace('/\s+/', '', $validEmail);
            if (filter_var($validEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    $transport = $this->_transportBuilder->setTemplateIdentifier($adminTemplate)
                        ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                        ->setTemplateVars($templateVar)
                        ->setFrom($sender)
                        ->addTo($validEmail);

                    if ($dataID->getAttechmentFile() != '') {
                        $fileUrlContent = $this->getAttementFilesArray('/'.$dataID->getAttechmentFile());
                        //@codingStandardsIgnoreStart
                        $fileContent = file_get_contents($fileUrlContent);
                        //@codingStandardsIgnoreEnd
                        $fileInfo = $this->file->getPathInfo($fileUrlContent);
                        $fileName = $fileInfo['basename'];
                        $fileType = $this->getFileType($fileInfo['extension']);
                        $transport = $this->_transportBuilder->addAttachment($fileContent, $fileName, $fileType);
                    }

                    $transport = $this->_transportBuilder->getTransport();
                    $transport->sendMessage();
                    if (isset($templateVar['inq_reply'])) {
                        $this->messageManager->addSuccessMessage("Email has been sent successfully.");
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Email send failed.'));
                }
                $emailCount++;
            }
        }
        if ($emailCount == 0) {
            $this->messageManager->addErrorMessage(__('Admin Email are Invalid or Empty.'));
        }
    }

    /**
     * Get Email Template Variables
     *
     * @param int $dataID
     * @return array
     * @throws NoSuchEntityException
     */
    public function getEmailTemplateVariable($dataID)
    {
        $prdCollection = $this->product->create()->load($dataID->getPrdEntityId());
        $templateVar['prd_name'] = $prdCollection->getName();
        $templateVar['prd_sku'] = $prdCollection->getSku();
        $templateVar['prd_url'] = $prdCollection->getProductUrl();
        $templateVar['store_view'] = $this->_storeManager->getStore()->getName();
        $templateVar['usr_name'] = $dataID->getUsrName();
        $templateVar['email'] = $dataID->getEmail();
        $templateVar['subject'] = $dataID->getSubject();
        $templateVar['inq_msg'] = $dataID->getInqMsg();
        if ($dataID->getAttechmentFile() != '') {
            $templateVar['attech'] = $dataID->getAttechmentFile();
        }
        return $templateVar;
    }

    /**
     * Get Attachment Files Array
     *
     * @param string $fileName
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttementFilesArray($fileName)
    {
        $filePath = $this->getHelpdeskFilePath();
        $file = $filePath . "" . $fileName;
        return $file;
    }

    /**
     * Get Help Desk FilePath
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getHelpdeskFilePath()
    {
        $media = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $media .= "Product_Inquiry_Attachments";
        return $media;
    }

    /**
     * Getting File Type
     *
     * @param string $fileExtension
     * @return mixed|string
     */
    public function getFileType($fileExtension)
    {
        // for file Type
        $fileType = '';
        $ftype = [];
        $ftype['pdf'] = "application/pdf";
        $ftype['csv'] = "text/csv";
        $ftype['xls'] = "application/vnd.ms-excel";
        $ftype['doc'] = "application/msword";
        $ftype['docx'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $ftype['txt'] = "text/plain";
        $ftype['jpg'] = "image/jpg";
        $ftype['jpeg'] = "image/jpeg";
        $ftype['gif'] = "image/gif";
        $ftype['png'] = "image/png";
        foreach ($ftype as $key => $validFileType) {
            if ($key == $fileExtension) {
                $fileType = $validFileType;
                break;
            }
        }
        return $fileType;
    }
}
