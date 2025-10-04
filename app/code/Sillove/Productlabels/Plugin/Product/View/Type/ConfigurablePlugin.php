<?php

namespace Sillove\Productlabels\Plugin\Product\View\Type;

use Magento\Framework\Json\DecoderInterface;
use Sillove\Productlabels\Helper\ProductLabeHelper;
use Magento\Framework\Json\EncoderInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class ConfigurablePlugin
{
    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var ProductLabeHelper
     */
    protected $helper;

    /**
     * @param DecoderInterface $jsonDecoder
     * @param ProductLabeHelper $helper
     * @param EncoderInterface $jsonEncoder
     */
    public function __construct(
        DecoderInterface $jsonDecoder,
        ProductLabeHelper $helper,
        EncoderInterface $jsonEncoder
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->helper = $helper;
    }

    /**
     * Plugin to modify the JSON configuration data returned by the getJsonConfig method
     *
     * @param Configurable $subject
     * @param array $result
     * @return array
     */
    public function afterGetJsonConfig(
        Configurable $subject,
        $result
    ) {
        $result = $this->jsonDecoder->decode($result);
        $currentProduct = $subject->getProduct();
        $module_status = $this->helper->getModuleStatus();
        if ($module_status == 1) {
            $result['productDiscount'] = 0;
            foreach ($subject->getAllowProducts() as $product) {

                $basePrice = number_format($product->getPrice());
                $specialPrice = number_format($product->getFinalPrice());
                if ($basePrice != $specialPrice) {
                    $discount = $this->helper->getDiscountPersentage($basePrice, $specialPrice);
                    $discount = $discount."%";
                } else {
                    $discount = "";
                }
                $result['discount'][$product->getId()][] =
                    [
                        'discount' => $discount,
                    ];
            }
        }

        return $this->jsonEncoder->encode($result);
    }
}
