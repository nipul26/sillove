<?php

namespace Sillove\Productlabels\Model;

use Sillove\Productlabels\Model\ResourceModel\ProductLabel as ProductLabelResourceModel;
use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as ProductCombineCoolection;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class ProductLabel extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'sillove_productlabels';

    /**
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * @var CombineFactory
     */
    protected $condCombineFactory;
   
    /**
     * @var ProductCombineCoolection
     */
    protected $condProdCombineF;

    /**
     * @var array
     */
    protected $validatedAddresses = [];
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $condCombineFactory
     * @param ProductCombineCoolection $condProdCombineF
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $condCombineFactory,
        ProductCombineCoolection $condProdCombineF,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ProductLabelResourceModel::class);
        $this->setIdFieldName('label_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return CombineFactory
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return ProductCombineCoolection
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }

    /**
     * Get Condition Fieldset
     *
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }
}
