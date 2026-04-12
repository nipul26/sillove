<?php
namespace Magecomp\Discountpercentage\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Discountpercentage\Helper\Data;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'magecomp_custom_text' => [
                'enabled' => $this->helper->isCheckoutCustomTextEnabled(),
                'label'   => $this->helper->getCheckoutLabel(),
                'text'    => $this->helper->getCheckoutCustomText()
            ]
        ];
    }
}
