<?php
namespace Sillove\MobileOtp\Controller\Ajax;

use Sillove\MobileOtp\Helper\Data;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

class Register implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerInterfaceFactory $customerFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Data $helper
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
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

        $verifiedMobile = $this->customerSession->getVerifiedMobileNumber();
        if (!$verifiedMobile) {
            return $result->setData([
                'success' => false,
                'message' => __('Please verify your mobile number before registering.')
            ]);
        }

        $firstname = trim((string) $this->request->getParam('firstname'));
        $lastname = trim((string) $this->request->getParam('lastname'));
        $email = trim((string) $this->request->getParam('email'));
        $password = (string) $this->request->getParam('password');
        $confirmPassword = (string) $this->request->getParam('password_confirmation');

        if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
            return $result->setData([
                'success' => false,
                'message' => __('Please fill in all required fields.')
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $result->setData([
                'success' => false,
                'message' => __('Please enter a valid email address.')
            ]);
        }

        if ($password !== $confirmPassword) {
            return $result->setData([
                'success' => false,
                'message' => __('Password and Confirm Password do not match.')
            ]);
        }

        try {
            $store = $this->storeManager->getStore();
            $customer = $this->customerFactory->create();
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setEmail($email);
            $customer->setStoreId($store->getId());
            $customer->setWebsiteId($store->getWebsiteId());

            $customer = $this->accountManagement->createAccount($customer, $password);
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->unsVerifiedMobileNumber();

            return $result->setData([
                'success' => true,
                'message' => __('Account created successfully. Logging you in...'),
                'redirect' => $this->urlBuilder->getUrl('customer/account')
            ]);
        } catch (LocalizedException $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Unable to create account. Please try again.')
            ]);
        }
    }
}
