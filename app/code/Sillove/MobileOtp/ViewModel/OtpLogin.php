<?php
namespace Sillove\MobileOtp\ViewModel;

use Sillove\MobileOtp\Helper\Data;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class OtpLogin implements ArgumentInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Data $helper,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    public function getMobileLoginUrl()
    {
        return $this->urlBuilder->getUrl('mobileotp/account/login');
    }

    public function getEmailLoginUrl()
    {
        return $this->urlBuilder->getUrl('customer/account/login');
    }

    public function getPrivacyPolicyUrl()
    {
        $path = $this->helper->getPrivacyPolicyPath();
        if (!$path) {
            $path = 'privacy-policy';
        }
        if (filter_var($path, FILTER_VALIDATE_URL) || strpos($path, '/') === 0) {
            return $path;
        }
        return $this->urlBuilder->getUrl($path);
    }

    public function getTermsUrl()
    {
        $path = $this->helper->getTermsPath();
        if (!$path) {
            $path = 'terms-and-conditions';
        }
        if (filter_var($path, FILTER_VALIDATE_URL) || strpos($path, '/') === 0) {
            return $path;
        }
        return $this->urlBuilder->getUrl($path);
    }

    public function getMinimumPasswordLength()
    {
        return (int) $this->scopeConfig->getValue(
            AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getRequiredCharacterClassesNumber()
    {
        return (int) $this->scopeConfig->getValue(
            AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER,
            ScopeInterface::SCOPE_STORE
        );
    }
}
