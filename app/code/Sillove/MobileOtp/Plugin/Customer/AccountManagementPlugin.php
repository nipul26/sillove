<?php
namespace Sillove\MobileOtp\Plugin\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Sillove\MobileOtp\Helper\Data as Helper;

class AccountManagementPlugin
{
    protected $customerSession;
    protected $helper;

    public function __construct(CustomerSession $customerSession, Helper $helper)
    {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
    }

    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        if (!$this->helper->isEnabled()) {
            return [$customer, $password, $redirectUrl];
        }

        $verifiedMobile = $this->customerSession->getVerifiedMobileNumber();
        if ($verifiedMobile) {
            $customer->setCustomAttribute('mobile_number', $verifiedMobile);
        }
        return [$customer, $password, $redirectUrl];
    }
}
