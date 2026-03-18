<?php
namespace Shiprocket\Checkout\Block\Adminhtml\OrderLog\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;

class Link extends AbstractRenderer
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Renders the column data as an HTML link
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $url = $this->_urlBuilder->getUrl(
            'sales/order/view',
            ['order_id' => $row->getOid()]
        );

        return '<a target="_blank" href="' . $url . '">' . $row->getOrderId() . '</a>';
    }
}
