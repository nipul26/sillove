<?php
/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * ViewModel for providing preselect configuration to frontend templates.
 *
 * @category  Magepow
 * @package   Magepow_ConfigurablePreselect
 */
declare(strict_types=1);

namespace Magepow\ConfigurablePreselect\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * ViewModel that exposes module configuration to templates.
 *
 * Replaces direct helper usage in .phtml files with a clean ViewModel approach.
 */
class PreselectConfig implements ArgumentInterface
{
    private const XML_PATH_ENABLED = 'magepow_configurablepreselect/general/enabled';
    private const XML_PATH_TYPE_PRESELECTED = 'magepow_configurablepreselect/general/type_preselected';
    private const XML_PATH_ENABLED_CATEGORY_PAGE = 'magepow_configurablepreselect/general/enabled_categorypage';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check whether the module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the preselection type (1 = Default, 2 = Highest Price, 3 = Lowest Price).
     *
     * @return string
     */
    public function getTypePreselected(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_TYPE_PRESELECTED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether category page preselection is enabled.
     *
     * @return bool
     */
    public function isCategoryPageEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED_CATEGORY_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
