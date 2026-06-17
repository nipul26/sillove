<?php
namespace Sillove\Excludejs\Plugin;

use Magento\Framework\View\Asset\Minification;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ExcludeJsFromMinification
{
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType != 'js' && !$subject->isEnabled($contentType)) {
            return $result;
        }
        
        $result = [
            'Magiccart_Magicmenu/js/magicmenu'
        ];
         
        return $result;
    }
}
