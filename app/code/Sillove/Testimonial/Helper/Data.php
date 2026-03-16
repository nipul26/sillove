<?php
namespace Sillove\Testimonial\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;

    /**
     * __construct
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
    ) {
        parent::__construct($context);
        $module = strtolower(str_replace('Sillove_', '', $this->_getModuleName()));
        $this->configModule = $this->getConfig($module);
    }

    /**
     * @inheritdoc
     */
    public function getConfig($cfg = '')
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getConfigModule($cfg = '', $value = null)
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }
        $config  = explode('/', $cfg);
        $end     = count($config) - 1;
        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key == $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }

        }
        return $value;
    }
}
