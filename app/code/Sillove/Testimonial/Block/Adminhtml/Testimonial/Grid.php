<?php
namespace Sillove\Testimonial\Block\Adminhtml\Testimonial;

use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Review\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Review\Helper\Data
     */
    protected $reviewData = null; //NOSONAR

    /**
     * @var \Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory
     */
    protected $testimonialCollectionFactory; //NOSONAR

    /**
     * __construct
     *
     * @param Context           $context
     * @param BackendHelper     $backendHelper
     * @param Data              $reviewData
     * @param CollectionFactory $testimonialCollectionFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        Data $reviewData,
        CollectionFactory $testimonialCollectionFactory,
        array $data = []
    ) {

        $this->reviewData = $reviewData;
        $this->testimonialCollectionFactory = $testimonialCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('testimonial_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $store = $this->getRequest()->getParam('store');
        $collection = $this->testimonialCollectionFactory->create();
        if ($store) {
            $collection->addFieldToFilter('stores', [ ['finset' => 0], ['finset' => $store]]);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Grid Columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'testimonial_id',
            [
                'header' => __('Testimonial ID'),
                'type' => 'number',
                'index' => 'testimonial_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );

        $this->addColumn('rating_summary', [
            'header'    => __('Rating'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'rating_summary',
            'type'      => 'options',
            'renderer'  => \Sillove\Testimonial\Block\Adminhtml\Helper\Renderer\Grid\Summary::class,
            'options'   => [
              1 => '1 star',
              2 => '2 stars',
              3 => '3 stars',
              4 => '4 stars',
              5 => '5 stars',
            ],
        ]);

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->reviewData->getReviewStatuses(),
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Last Update At'),
                'type' => 'text',
                'index' => 'updated_at',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'testimonial_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Get testimonial vailable option
     *
     * @return array
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonial');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('testimonial/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        $statuses = $this->reviewData->getReviewStatuses();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('testimonial/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses,
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * Get Grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Get Row URL
     *
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['testimonial_id' => $row->getId()]
        );
    }
}
