<?php
namespace Sillove\DynamicHeader\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Links extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('title', ['label' => __('Title'), 'class' => 'required-entry']);
        $this->addColumn('url', ['label' => __('URL'), 'class' => 'required-entry']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Link');
    }
}