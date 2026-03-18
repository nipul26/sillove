<?php
/**
 * Shiprocket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the shiprocket.in license that is
 * available through the world-wide-web at this URL:
 * https://checkout.shiprocket.in/magento-license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Shiprocket
 * @package     Shiprocket_Checkout
 * @copyright   Copyright (c) Shiprocket (https://www.shiprocket.in/)
 * @license     https://checkout.shiprocket.in/magento-license
 */
declare(strict_types=1);
namespace Shiprocket\Checkout\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Shiprocket\Checkout\Block\Adminhtml\System\Config\AccessKey;
use Magento\Backend\Block\Widget\Button;
 
class AccessKey extends Field
{
    /**
     * @var \Magento\Framework\UrlInterface|null
     */
    protected $_urlBuilder = null;

    /**
     * @var BUTTON_TEMPLATE
     */
    protected const BUTTON_TEMPLATE = 'system/config/button/access-key.phtml';
  
    /**
     * Get element html
     *
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData([
            'id' => 'srcheckout_get_access_key_button',
            'label' => __('Get Access Key'),
        ])
            ->setDataAttribute(
                ['role' => 'srcheckout-get-access-key-button']
            );
 
        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->_layout->createBlock(AccessKey::class);
        $block->setTemplate(static::BUTTON_TEMPLATE)
            ->setChild('button', $button)
            ->setData('select_html', parent::_getElementHtml($element));
 
        return $block->toHtml();
    }
 
    /**
     * Get access key ajax url
     *
     * @return string
     */
    public function getAjaxAccessKeyUrl()
    {
        return $this->_urlBuilder->getUrl('srcheckout/checkout/accesstoken');
    }
}
