<?php
namespace Sillove\MobileOtp\Plugin\Customer;

use Magento\Customer\Model\Url;
use Magento\Framework\UrlInterface;
use Sillove\MobileOtp\Helper\Data;

class LoginUrlPlugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(Data $helper, UrlInterface $urlBuilder)
    {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Point header and storefront login links to the mobile OTP page.
     *
     * @param Url $subject
     * @param string $result
     * @return string
     */
    public function afterGetLoginUrl(Url $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        return $this->urlBuilder->getUrl('mobileotp/account/login');
    }
}
