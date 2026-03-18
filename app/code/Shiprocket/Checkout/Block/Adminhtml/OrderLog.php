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
namespace Shiprocket\Checkout\Block\Adminhtml;

use Shiprocket\Checkout\Block\Adminhtml\OrderLog\Grid;

class OrderLog extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'orderlog.phtml';

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(Grid::class, 'srcheckout.orderlog.grid')
        );
        $this->addExportButton();
        return parent::_prepareLayout();
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Add export button
     *
     * @return void
     */
    private function addExportButton()
    {
        $this->addButton('export_csv', [
            'label' => __('Export to CSV'),
            'onclick' => 'setLocation(\'' . $this->getExportCsvUrl() . '\')',
            'class' => 'primary',
        ]);
    }

    /**
     * Get export csv url
     *
     * @return string
     */
    public function getExportCsvUrl()
    {
        $filters = $this->getRequest()->getParam('orderlog_filter');
        if ($filters) {
            return $this->getUrl('*/*/exportCsv', ['orderlog_filter' => $filters]);
        }

        return $this->getUrl('*/*/exportCsv');
    }
}
