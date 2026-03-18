<?php

namespace Shiprocket\Checkout\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Integration extends Field
{
    /**
     * Render the custom HTML content
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '<div><a href="https://docs.google.com/forms/d/1JPKkzdeDQNyrFZaL7c60BaIkpbtF3LOCbLKb68LMB6s/"';
        $html .= 'target="_blank">';
        $html .= '<img src="https://xank-cdn.s3.ap-southeast-1.amazonaws.com/shiprocket-contactus.png">';
        $html .= '</a></div>';
        
        return $html;
    }
}
