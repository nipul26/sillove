<?php
namespace Magecomp\Discountpercentage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    protected $scopeConfig;
    protected $_storeManager;

    public function __construct( Context $context,
                                 \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    public function isActive()
    {
        return $this->scopeConfig->getValue('discountpercentage/general/enable',
            ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return string|null
     */
    public function getFreeShippingText()
    {
        return $this->scopeConfig->getValue(
            'discountpercentage/general/free_shipping_text',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }
}