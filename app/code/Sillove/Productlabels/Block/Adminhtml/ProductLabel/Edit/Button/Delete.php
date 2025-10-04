<?php

namespace Sillove\Productlabels\Block\Adminhtml\ProductLabel\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Retrieve button data.
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $label_id = $this->context->getRequest()->getParam('label_id');
        if ($label_id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Retrieve delete URL.
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        $label_id = $this->context->getRequest()->getParam('label_id');
        return $this->getUrl('*/*/delete', ['label_id' => $label_id]);
    }
}
