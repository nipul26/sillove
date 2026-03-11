<?php
namespace Sillove\PageSpeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * Get settings
     */
    public function getSettings()
    {

        $settings = null;

        $settings1 = $this->scopeConfig->getValue(
            'Sillove_pagespeed_general_setting/general',
            ScopeInterface::SCOPE_STORE
        );
        if ($settings1) {
            $settings = $settings1;
        }

        $settings3 = $this->scopeConfig->getValue(
            'Sillove_pagespeed_javascript_opt_settings/general',
            ScopeInterface::SCOPE_STORE
        );
        if ($settings3) {
            $settings = array_merge($settings, $settings3);
        }

        if ($settings) {
            return $settings;
        }

        return false;
    }
}
