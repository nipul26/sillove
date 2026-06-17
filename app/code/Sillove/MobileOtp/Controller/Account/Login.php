<?php
namespace Sillove\MobileOtp\Controller\Account;

use Sillove\MobileOtp\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Login implements HttpGetActionInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        Data $helper
    ) {
        $this->context = $context;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
    }

    /**
     * Mobile OTP sign-in page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            $resultRedirect = $this->context->getResultRedirectFactory()->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        if ($this->session->isLoggedIn()) {
            $resultRedirect = $this->context->getResultRedirectFactory()->create();
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Sign In'));
        $resultPage->setHeader('Login-Required', 'true');
        return $resultPage;
    }
}
