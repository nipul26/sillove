<?php

namespace Sillove\Productlabels\Block\Catalog\Product\View;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Sillove\Productlabels\Helper\ProductLabeHelper;
use Magento\Catalog\Model\Product;

class LabelData extends Template
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var ProductLabeHelper
     */
    protected $productlabeHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param ProductLabeHelper $productlabeHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductLabeHelper $productlabeHelper
    ) {
        parent::__construct($context);
        $this->productlabeHelper = $productlabeHelper;
        $this->registry = $registry;
    }

    /**
     * Retrieve the currently viewed product from the registry.
     *
     * @return Product|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Retrieve the product label helper.
     *
     * @return ProductLabeHelper
     */
    public function getProductLabelHelper()
    {
        return $this->productlabeHelper;
    }
}
