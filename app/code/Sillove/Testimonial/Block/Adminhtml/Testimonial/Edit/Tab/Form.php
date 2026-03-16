<?php
// phpcs:disable Generic.Files.LineLength.TooLong
namespace Sillove\Testimonial\Block\Adminhtml\Testimonial\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use Magento\Review\Helper\Data;

class Form extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null; //NOSONAR

    /**
     * __construct
     *
     * @param Context           $context
     * @param Registry          $registry
     * @param FormFactory       $formFactory
     * @param Store             $systemStore
     * @param Data              $reviewData
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $reviewData,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->_reviewData = $reviewData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Layout prepare
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('testimonial');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

        if ($model->getId()) {
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Name'),
                'title' => __('Name'),
                'name'  => 'name',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'designation',
            'text',
            [
                'label' => __('Designation'),
                'title' => __('Designation'),
                'name'  => 'designation',
                'required' => false,
            ]
        );

        $fieldset->addField(
            'text',
            'editor',
            [
                'label' => __('Testimonial Message'),
                'title' => __('Testimonial Message'),
                'name'  => 'text',
                'required' => true,
            ]
        );

        $summary = $this->getLayout()->createBlock(\Sillove\Testimonial\Block\Adminhtml\Helper\Renderer\Form\Summary::class);
        $fieldset->addField('detailed_rating', 'note', [
            'label'     => __('Detailed Rating'),
            'text'      => $summary->detailedHtml(),
        ]);

        $fieldset->addField(
            'order',
            'text',
            [
                'label' => __('Sort-Order'),
                'title' => __('Sort-Order'),
                'name'  => 'order',
            ]
        );

        /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => $this->_reviewData->getReviewStatuses(),
            ]
        );

        $form->addValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get testimonial
     *
     * @return mixed
     */
    public function getTestimonial()
    {
        return $this->_coreRegistry->registry('testimonial');
    }

    /**
     * Get page Tittle
     *
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getTestimonial()->getId()
            ? __("Edit Testimonial '%1'", $this->escapeHtml($this->getTestimonial()->getName())) : __('New Testimonial');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }
}
