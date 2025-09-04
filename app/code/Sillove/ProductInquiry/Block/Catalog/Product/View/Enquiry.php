<?php

namespace Sillove\ProductInquiry\Block\Catalog\Product\View;

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Sillove\ProductInquiry\Helper\ProductInquiryHelper;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\Template;

class Enquiry extends Template
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Session
     */
    protected $_session;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductInquiryHelper
     */
    protected $_Productinquiryhelper;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var HttpContext
     */
    protected $httpContext;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * Enquiry Constructor
     *
     * @param Context $context
     * @param HttpContext $httpContext
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ProductInquiryHelper $Productinquiryhelper
     * @param DataPersistorInterface $dataPersistor
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ProductInquiryHelper $Productinquiryhelper,
        DataPersistorInterface $dataPersistor,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->httpContext = $httpContext;
        $this->registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_Productinquiryhelper = $Productinquiryhelper;
        $this->dataPersistor = $dataPersistor;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Get Current StoreId
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreID()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get Current Customer
     *
     * @return Customer
     */
    public function getCurrentCustomer()
    {
        $customerId = $this->httpContext->getValue('context_customer_id');

        return $this->customerFactory->create()->load($customerId);
    }

    /**
     * Get Current UserEmail
     *
     * @return string
     */
    public function getCurrentUserEmail()
    {
        return $this->getCurrentCustomer()->getEmail();
    }

    /**
     * Get Current Username
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCurrentUserName()
    {
        return $this->getCurrentCustomer()->getName();
    }

    /**
     * Get User Status
     *
     * @return bool
     */
    public function getUserStatus()
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Get Current Product
     *
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Tab Title
     *
     * @param string $title
     * @return void
     */
    public function setTabTitle($title)
    {
        $this->setTitle($title);
    }

    /**
     * Product Inquiry Helper
     *
     * @return ProductInquiryHelper
     */
    public function getInquiryHelper()
    {
        return $this->_Productinquiryhelper;
    }

    /**
     * Get All Data After Error
     *
     * @return mixed
     */
    public function getAllDataAfterError()
    {
        return $this->dataPersistor->get('inquiry_form_data');
    }
}
