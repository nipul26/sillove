<?php

namespace Codezspark\CouponList\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const XML_PATH_MODULE_STATUS_CONFIG = 'couponlist/general/enable';
    public const XML_PATH_SHOW_COUPONS_CONFIG = 'couponlist/general/show_coupons';
    public const XML_PATH_PDP_COUPON_LIST_CONFIG = 'couponlist/general/enable_pdp';

    /**
     * @var StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * Constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $httpContext
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Context $httpContext
    ) {
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->httpContext = $httpContext;
    }

    /**
     * Get the store ID.
     *
     * @return int
     */

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get the value of a configuration field.
     *
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get the status configuration value of the module.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getModuleStatusConfig($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MODULE_STATUS_CONFIG, $storeId);
    }

    /**
     * Get the show coupons configuration value.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getShowCouponsConfig($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SHOW_COUPONS_CONFIG, $storeId);
    }

    /**
     * Get the PDP coupon list configuration value.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getPdpCouponListConfig($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PDP_COUPON_LIST_CONFIG, $storeId);
    }

    /**
     * Get the ID of the customer group.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->httpContext->getValue('customer_group');
    }

    /**
     * Get the website ID of the current store.
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
}
