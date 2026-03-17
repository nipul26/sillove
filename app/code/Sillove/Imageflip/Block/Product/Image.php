<?php

namespace Sillove\Imageflip\Block\Product;

use Sillove\Imageflip\Helper\Data;
use Sillove\Productlabels\Block\Product\Image as ProductLabelsImage;
use Sillove\Productlabels\Helper\ProductLabeHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\View\Element\Template\Context;

class Image extends ProductLabelsImage
{
    /**
     * @var ProductFactory
     */
    protected $productColl;

    /**
     * @var UrlBuilder
     */
    protected $imageUrlBuilder;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Data $helperData
     * @param Context $context
     * @param ProductLabeHelper $productlabehelper
     * @param ProductFactory $productColl
     * @param UrlBuilder $urlBuilder
     * @param array $data
     */
    public function __construct(
        Data $helperData,
        Context $context,
        ProductLabeHelper $productlabehelper,
        ProductFactory $productColl,
        UrlBuilder $urlBuilder,
        array $data = []
    ) {
        if (isset($data['template'])) {
            $this->setTemplate('Sillove_Imageflip::imageflip.phtml');
            unset($data['template']);
        }
        $this->productColl = $productColl;
        $this->imageUrlBuilder = $urlBuilder;
        $this->helperData = $helperData;
        parent::__construct($context, $productlabehelper, $data);
    }

    /**
     * [getIsEnable Return module status(enable/disable)]
     *
     * @return [type] [description]
     */
    public function getIsEnable()
    {
        return $this->helperData->getEnable();
    }

    /**
     * [getFlipvalue Return image flip value]
     *
     * @return [type] [description]
     */
    public function getFlipvalue()
    {
        return $this->helperData->getFlipimage();
    }

    /**
     * [getAutoselect Auto Select Flip Image]
     *
     * @return [type] [description]
     */
    public function getAutoselect()
    {
        return $this->helperData->getAutoselect();
    }

    /**
     * [getFlipImageUrl Get flip image url and imageflip value from product]
     *
     * @param  [int] $product_id [Product Id]
     * @return [array]           [description]
     */
    public function getFlipImageUrl($product_id)
    {
        $data = [];
        $product_data = $this->productColl->create()->load($product_id);
        $data['imageflip'] = $product_data->getImageflip();
        $flipImg = $product_data->getImageflip();
        if ($this->getAutoselect()) {
            $images = $product_data->getMediaGalleryImages();
            $secondImage = '';
            $i = 0;
            foreach ($images as $child) {
                if ($i == 1) {
                    $secondImage = $child->getFile();
                }
                $i++;
            }
            if ($data['imageflip'] == 'no_selection' || $data['imageflip'] == '') {
                if ($secondImage != '') {
                    $data['imageflip'] = $secondImage;
                    $flipImg = $secondImage;
                }
            }
        }
        if ($flipImg) {
            $flipImg = $this->imageUrlBuilder->getUrl($flipImg, 'category_page_grid');
        }
        $data['flipimgurl'] = $flipImg;

        return $data;
    }

    /**
     * [getProductData Return single product collection using product id]
     *
     * @param  [int] $product_id [Product Id]
     * @return [object]             [Return product object data]
     */
    public function getProductData($product_id)
    {
        return $this->productColl->create()->load($product_id);
    }
}
