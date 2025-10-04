<?php

namespace Sillove\Productlabels\Block\Adminhtml\ProductLabel\Catalog\ConditionField;

use Sillove\Productlabels\Model\ProductLabelFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleBlockConditions;
use Magento\Rule\Model\Condition\AbstractCondition;

class Conditions extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var RuleBlockConditions
     */
    protected $conditions;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions';

    /**
     * @var array
     */
    private $ruleFactory;

    /**
     * @var ProductLabelFactory
     */
    private $productlabelFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Conditions $conditions
     * @param Fieldset $rendererFieldset
     * @param ProductLabelFactory $productlabelFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleBlockConditions $conditions,
        Fieldset $rendererFieldset,
        ProductLabelFactory $productlabelFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->productlabelFactory = $productlabelFactory;
    }

    /**
     * Get rule factory.
     *
     * @return ProductLabelFactory|null
     */
    private function getRuleFactory()
    {
        if ($this->ruleFactory === null) {

            $this->ruleFactory = $this->productlabelFactory;

        }
        return $this->ruleFactory;
    }

    /**
     * Get tab class.
     *
     * @return null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Get tab url.
     *
     * @return null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Check if ajax loaded.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Get tab label.
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Get tab title.
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Check if tab can be shown.
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare the form before rendering.
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_rule');
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add conditions tab to form.
     *
     * @param mixed $model
     * @param string $fieldsetId
     * @param string $formName
     * @return FormFactory
     */
    protected function addTabToForm(
        $model,
        $fieldsetId = 'conditions_serialized_field',
        $formName = 'productlable_form_edit'
    ) {
        if (!$model) {
            $id = $this->getRequest()->getParam('label_id');
            $model = $this->getRuleFactory()->create();
            $model->load($id);
        }
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'catalog_rule/promo_catalog/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var FormFactory $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                ),
            ]
        )->setRenderer(
            $renderer
        );
        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName,
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );
        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);

        return $form;
    }

    /**
     * Set form name for conditions.
     *
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
