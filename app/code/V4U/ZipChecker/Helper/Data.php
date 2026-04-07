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
    const CONFIG_IS_ENABLED = 'zipchecker/config/is_enabled';
    /**
     *
     */
    const CONFIG_ZIPCODES = 'zipchecker/config/zipcodes';
    /**
     *
     */
    const CONFIG_SUCCESS_MESSAGE = 'zipchecker/config/success_message';
    /**
     *
     */
    const CONFIG_ERROR_MESSAGE = 'zipchecker/config/error_message';

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
}
