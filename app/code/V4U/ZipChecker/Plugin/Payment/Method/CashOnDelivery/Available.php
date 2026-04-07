<?php

namespace V4U\ZipChecker\Plugin\Payment\Method\CashOnDelivery;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Checkout\Model\Session as CheckoutSession;
use V4U\ZipChecker\Model\GridFactory;
use V4U\ZipChecker\Helper\Data;

class Available
{
    /**
     * @var GridFactory
     */
    protected $modelGridFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var DataHelper
     */
    /*protected $dataHelper;*/
    /**
     * @var BackendSession
     */
    protected $backendSession;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param CustomerSession $customerSession
     * @param BackendSession $backendSession
     * @param Data $helper
     */
    public function __construct(
        CustomerSession $customerSession,
        BackendSession $backendSession,
        CheckoutSession $checkoutSession,
        GridFactory $modelGridFactory,
        Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->backendSession = $backendSession;
        $this->checkoutSession  = $checkoutSession;
        $this->modelGridFactory = $modelGridFactory;
        $this->helper = $helper;
    }
    /**
     *
     * @param Cashondelivery $subject
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterIsAvailable(Cashondelivery $subject, $result)
    {
        // Do not remove payment method for admin
        if ($this->backendSession->isLoggedIn()) {
            return $result;
        }

        $isEnabled = $this->helper->getIsActive();
        if($isEnabled){
            $resultPage = $this->modelGridFactory->create();
            $collection = $resultPage->getCollection();
            $collection = $collection->addFieldToSelect('zipcode')->addFieldToFilter('is_active',array('eq'=>'1'));
            $zipcodes = array();
            foreach ($collection as $zipCodes) {
                $zipcodes[] =$zipCodes->getZipCode(); 
            }
            $zipcode = $this->checkoutSession->getQuote()->getShippingAddress()->getPostcode();
                if (in_array($zipcode,$zipcodes)){
                    return true;
                }
                else{
                    return false;
                } 
        }
        else{
            return true;
        }

    }
}