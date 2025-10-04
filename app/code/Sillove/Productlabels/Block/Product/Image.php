<?php

namespace Sillove\Productlabels\Block\Product;

use Magento\Catalog\Block\Product\Image as ProductImage;
use Magento\Framework\View\Element\Template\Context;
use Sillove\Productlabels\Helper\ProductLabeHelper;

class Image extends ProductImage
{
    /**
     * @var array
     */
    protected $imageHelper;
    /**
     * @var ProductLabeHelper
     */
    protected $productlabehelper;
    /**
     * @var array
     */
    protected $product;
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param ProductLabeHelper $productlabehelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductLabeHelper $productlabehelper,
        array $data = []
    ) {
        if (isset($data['template'])) {
            $this->setTemplate('Sillove_Productlabels::product/labelshow.phtml');
            unset($data['template']);
        }
        $this->productlabehelper = $productlabehelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the product label helper.
     *
     * @return ProductLabeHelper
     */
    public function getProductLabelhelper()
    {
        return $this->productlabehelper;
    }
}
