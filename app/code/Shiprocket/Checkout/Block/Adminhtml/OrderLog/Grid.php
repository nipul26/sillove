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
namespace Shiprocket\Checkout\Block\Adminhtml\OrderLog;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Shiprocket\Checkout\Model\OrderLog;
use Shiprocket\Checkout\Model\Status;
use Magento\Framework\Module\Manager;
use Shiprocket\Checkout\Block\Adminhtml\OrderLog\Grid\Renderer\Link;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var OrderLog
     */
    protected $orderlog;

    /**
     * @var Status
     */
    protected $status;
    
    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param OrderLog $orderlog
     * @param Status $status
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        OrderLog $orderlog,
        Status $status,
        array $data = []
    ) {
        $this->orderlog = $orderlog;
        $this->status = $status;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Sort grid data
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderlogGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('orderlog_filter');
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->orderlog->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare display column
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'sr_data',
            [
                'header' => __('Order data'),
                'index' => 'sr_data',
            ]
        );

        $this->addColumn(
            'sr_order_id',
            [
                'header' => __('Fastrr Order ID'),
                'index' => 'sr_order_id',
            ]
        );
        
        $this->addColumn(
            'payment_type',
            [
                'header' => __('Payment type'),
                'index' => 'payment_type',
            ]
        );
                
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order ID'),
                'index' => 'order_id',
                'renderer'  => Link::class,
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->toOptionArray(),
            ]
        );

        $this->addColumn(
            'errmsg',
            [
                'header' => __('Error'),
                'index' => 'errmsg',
            ]
        );
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index' => 'updated_at',
                'type' => 'datetime'
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('srcheckout/*/index', ['_current' => true]);
    }
}
