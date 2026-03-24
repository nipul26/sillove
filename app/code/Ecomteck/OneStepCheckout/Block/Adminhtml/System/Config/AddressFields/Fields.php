<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_OneStepCheckout
 * @copyright   Copyright (c) 2018 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
namespace Ecomteck\OneStepCheckout\Block\Adminhtml\System\Config\AddressFields;

class Fields extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Ecomteck\OneStepCheckout\Helper\AddressFields
     */
    protected $addressFieldsHelper;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Ecomteck\OneStepCheckout\Helper\AddressFields $addressFieldsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Ecomteck\OneStepCheckout\Helper\AddressFields $addressFieldsHelper,
        array $data = []
    ) {
        $this->addressFieldsHelper = $addressFieldsHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $fields = $this->addressFieldsHelper->getAddressFields();
        if (!$this->getOptions()) {
            $this->addOption(
                '',
                __('Please Select Field')
            );
            foreach ($fields as $field => $fieldName) {
                $this->addOption($field, addslashes($fieldName));
            }
        }
        return parent::_toHtml();
    }
}
