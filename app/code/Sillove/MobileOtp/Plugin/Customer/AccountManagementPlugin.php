<?php
namespace Sillove\MobileOtp\Plugin\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Psr\Log\LoggerInterface;
use Sillove\MobileOtp\Helper\Data as Helper;

class AccountManagementPlugin
{
    protected $customerSession;
    protected $helper;
    protected $logger;

    public function __construct(
        CustomerSession $customerSession,
        Helper $helper,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        try {
            if (!$this->helper->isEnabled()) {
                return [$customer, $password, $redirectUrl];
            }

            $verifiedMobile = $this->customerSession->getVerifiedMobileNumber();
            if ($verifiedMobile) {
                $customer->setCustomAttribute('mobile_number', $verifiedMobile);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error in AccountManagementPlugin: ' . $e->getMessage());
        }
        return [$customer, $password, $redirectUrl];
    }
}
