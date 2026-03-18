<?php

namespace Shiprocket\Checkout\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Features extends Field
{
    /**
     * Render the custom HTML content
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div>Help us improve the plugin! Share your ideas, vote on upcoming features, ';
        $html .= 'or report issues directly through our <a href="https://checkout-magento.canny.io/feature-requests" ';
        $html .= 'target="_blank">';
        $html .= 'Feature Request Board</a>';
        $html .= '<div>Your feedback shapes the future of this plugin. We review submissions regularly ';
        $html .= 'and integrate top-voted ideas into our roadmap.';
        $html .= '</div>';
        
        return $html;
    }
}
