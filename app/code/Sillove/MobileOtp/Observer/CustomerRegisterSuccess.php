<?php
namespace Sillove\MobileOtp\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session as CustomerSession;

class CustomerRegisterSuccess implements ObserverInterface
{
    protected $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        // Unset the verified mobile number so it isn't used for subsequent accounts
        $this->customerSession->unsVerifiedMobileNumber();
    }
}
