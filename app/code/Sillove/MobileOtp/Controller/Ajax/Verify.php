<?php
namespace Sillove\MobileOtp\Controller\Ajax;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Sillove\MobileOtp\Model\Msg91Api;
use Magento\Framework\UrlInterface;
use Sillove\MobileOtp\Helper\Data as Helper;

class Verify implements HttpPostActionInterface
{
    protected $request;
    protected $resultJsonFactory;
    protected $customerSession;
    protected $customerRepository;
    protected $searchCriteriaBuilder;
    protected $msg91Api;
    protected $urlBuilder;
    protected $customerFactory;
    protected $helper;


    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Msg91Api $msg91Api,
        UrlInterface $urlBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Helper $helper
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->msg91Api = $msg91Api;
        $this->urlBuilder = $urlBuilder;
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
    }


    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->helper->isEnabled()) {
            return $result->setData([
                'success' => false,
                'message' => __('Mobile OTP login is not available.')
            ]);
        }

        $mobileNumber = $this->request->getParam('mobile_number');
        $otp = $this->request->getParam('otp');

        if (!$mobileNumber || !$otp) {
            return $result->setData([
                'success' => false,
                'message' => __('Missing mobile number or OTP.')
            ]);
        }

        $apiResponse = $this->msg91Api->verifyOtp($mobileNumber, $otp);

        if (isset($apiResponse['type']) && $apiResponse['type'] === 'success') {
            
            // Check if customer exists with this mobile number
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('mobile_number', $mobileNumber)
                ->create();
            $customers = $this->customerRepository->getList($searchCriteria)->getItems();

            if (count($customers) > 0) {
                // Customer exists, log them in
                $customer = reset($customers);
                
                // Load customer model and log in
                $customerModel = $this->customerFactory->create()->load($customer->getId());
                $this->customerSession->setCustomerAsLoggedIn($customerModel);
                $this->customerSession->setCustomer($customerModel); // Additional session setting
                $this->customerSession->regenerateId();

                return $result->setData([
                    'success' => true,
                    'message' => __('Verified successfully. Logging you in...'),
                    'redirect' => $this->urlBuilder->getUrl('customer/account')
                ]);
            } else {
                $this->customerSession->setVerifiedMobileNumber($mobileNumber);

                return $result->setData([
                    'success' => true,
                    'needs_registration' => true,
                    'message' => __('OTP verified. Please complete your registration.'),
                    'mobile_number' => $mobileNumber
                ]);
            }
        }

        return $result->setData([
            'success' => false,
            'message' => $apiResponse['message'] ?? __('Invalid OTP.')
        ]);
    }
}
