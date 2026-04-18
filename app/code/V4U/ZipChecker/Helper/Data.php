<?php
namespace V4U\ZipChecker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package V4U\ZipChecker\Helper
 */

class Data extends AbstractHelper
{
    /**
     *
     */
    const CONFIG_IS_ENABLED = 'zipchecker/general/is_enabled';
    /**
     *
     */
    const CONFIG_ZIPCODES = 'zipchecker/general/zipcodes';
    /**
     *
     */
    const CONFIG_SUCCESS_MESSAGE = 'zipchecker/general/success_message';
    /**
     *
     */
    const CONFIG_ERROR_MESSAGE = 'zipchecker/general/error_message';
    /**
     *
     */
    const CONFIG_API_EMAIL = 'zipchecker/general/api_email';
    /**
     *
     */
    const CONFIG_API_PASSWORD = 'zipchecker/general/api_password';
    /**
     *
     */
    const CONFIG_PICKUP_POSTCODE = 'zipchecker/general/pickup_postcode';
    /**
     *
     */
    const CONFIG_COD = 'zipchecker/general/cod';
    /**
     *
     */
    const CONFIG_WEIGHT = 'zipchecker/general/weight';
    /**
     *
     */
    const CONFIG_AUTH_API_URL = 'zipchecker/general/auth_api_url';
    /**
     *
     */
    const CONFIG_SERVICEABILITY_API_URL = 'zipchecker/general/serviceability_api_url';

    /**
     * @var ScopeConfig
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param ScopeConfig $scopeConfig
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param string $storePath
     * @return mixed
     */
    public function getStoreConfig($storePath)
    {
        return $this->_scopeConfig->getValue(
            $storePath,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getZipCodes()
    {
        return trim($this->getStoreConfig(self::CONFIG_ZIPCODES));
    }

    /**
     * @return mixed
     */
    public function getSuccessMessage()
    {
        return $this->getStoreConfig(self::CONFIG_SUCCESS_MESSAGE);
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->getStoreConfig(self::CONFIG_ERROR_MESSAGE);
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->getStoreConfig(self::CONFIG_IS_ENABLED);
    }

    /**
     * @return string
     */
    public function getApiEmail()
    {
        return (string) $this->getStoreConfig(self::CONFIG_API_EMAIL);
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return (string) $this->getStoreConfig(self::CONFIG_API_PASSWORD);
    }

    /**
     * @return string
     */
    public function getPickupPostcode()
    {
        return trim((string) $this->getStoreConfig(self::CONFIG_PICKUP_POSTCODE));
    }

    /**
     * @return int
     */
    public function getCod()
    {
        return (int) $this->getStoreConfig(self::CONFIG_COD);
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return (float) $this->getStoreConfig(self::CONFIG_WEIGHT);
    }

    /**
     * @return string
     */
    public function getAuthApiUrl()
    {
        return trim((string) $this->getStoreConfig(self::CONFIG_AUTH_API_URL));
    }

    /**
     * @return string
     */
    public function getServiceabilityApiUrl()
    {
        return trim((string) $this->getStoreConfig(self::CONFIG_SERVICEABILITY_API_URL));
    }
}
