<?php
/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Module configuration helper.
 *
 * @category  Magepow
 * @package   Magepow_ConfigurablePreselect
 */
declare(strict_types=1);

namespace Magepow\ConfigurablePreselect\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper class for retrieving module configuration values.
 */
class Data extends AbstractHelper
{
    /**
     * @var array|mixed
     */
    private mixed $configModule;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
    }

    /**
     * Retrieve store configuration value or the scope config object.
     *
     * @param string $cfg Configuration path
     * @return mixed
     */
    public function getConfig(string $cfg = ''): mixed
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, ScopeInterface::SCOPE_STORE);
        }

        return $this->scopeConfig;
    }

    /**
     * Retrieve a specific module configuration value by path.
     *
     * Traverses the nested configuration array using a slash-delimited path.
     *
     * @param string $cfg   Slash-delimited config path (e.g. "general/enabled")
     * @param mixed  $value Default value if path not found
     * @return mixed
     */
    public function getConfigModule(string $cfg = '', mixed $value = null): mixed
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }

        $config = explode('/', $cfg);
        $end = count($config) - 1;

        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key === $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }
        }

        return $value;
    }
}
