<?php

namespace Sillove\Imageflip\Helper;

/**
 * image flip configuration
 * @method string getFlipimage()
 * @method string getEnable()
 * @method string getConfig()
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_IMAGEFLIPSEC_FLIPIMAGE = 'imageflip/settings/flipimage';
    public const XML_IMAGEFLIPSEC_ENABLE = 'imageflip/settings/enabled';
    public const XML_IMAGEFLIPSEC_AUTOSELECT = 'imageflip/settings/autoselect';

    /**
     * [getFlipimage Getrn Flip Image Value]
     *
     * @return [int] [System configuration of flip image]
     */
    public function getFlipimage()
    {
        return $this->getConfig(self::XML_IMAGEFLIPSEC_FLIPIMAGE);
    }

    /**
     * [getEnable description]
     *
     * @return [boolean] return enable/disable boolean value
     */
    public function getEnable()
    {
        return $this->getConfig(self::XML_IMAGEFLIPSEC_ENABLE);
    }
    /**
     * [getAutoselect description]
     *
     * @return [boolean] return 0/1 boolean value
     */
    public function getAutoselect()
    {
        return $this->getConfig(self::XML_IMAGEFLIPSEC_AUTOSELECT);
    }

    /**
     * [getConfig Prepare system config object]
     *
     * @param  [string] $path [System config field path]
     * @return [object]       [description]
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
