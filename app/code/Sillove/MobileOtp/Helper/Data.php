<?php
namespace Sillove\MobileOtp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_MODULE_ACTIVE = 'sillove_mobileotp/general/active';
    const XML_PATH_API_AUTHKEY = 'sillove_mobileotp/general/auth_token';
    const XML_PATH_API_TEMPLATE_ID = 'sillove_mobileotp/general/template_id';
    const XML_PATH_PRIVACY_POLICY_URL = 'sillove_mobileotp/general/privacy_policy_url';
    const XML_PATH_TERMS_URL = 'sillove_mobileotp/general/terms_url';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    public function __construct(Context $context, EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MODULE_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    public function getAuthKey()
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_API_AUTHKEY, ScopeInterface::SCOPE_STORE);
        return $this->encryptor->decrypt($value);
    }

    public function getTemplateId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_TEMPLATE_ID, ScopeInterface::SCOPE_STORE);
    }

    public function getPrivacyPolicyPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRIVACY_POLICY_URL, ScopeInterface::SCOPE_STORE);
    }

    public function getTermsPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TERMS_URL, ScopeInterface::SCOPE_STORE);
    }
}
