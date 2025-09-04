<?php

namespace Sillove\ProductInquiry\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Sillove\ProductInquiry\Helper\ProductInquiryHelper;

class RecapchaFormObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var ProductInquiryHelper
     */
    protected $helper;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * Recaptcha Form Constructor
     *
     * @param UrlInterface $url
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestHandlerInterface $requestHandler
     * @param RedirectInterface $redirect
     * @param ProductInquiryHelper $helper
     */
    public function __construct(
        UrlInterface $url,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestHandlerInterface $requestHandler,
        RedirectInterface $redirect,
        ProductInquiryHelper $helper
    ) {
        $this->url = $url;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->requestHandler = $requestHandler;
        $this->redirect = $redirect;
        $this->helper = $helper;
    }

    /**
     * Execute Method
     *
     * @param Observer $observer
     * @return void
     * @throws InputException
     */
    public function execute(Observer $observer): void
    {
        $key = 'productinquiry';
        $reCaptcha = $this->helper->getReCaptcha();
        if (($reCaptcha)) {
            $showType = $this->helper->getButtonTabOption();
            if ($showType == 'popup') {
                $key = 'productinquiry';
            } else {
                $key = 'productinquirytab';
            }
            // for check Recaptcha key
            if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
                /** @var Action $controller */
                $controller = $observer->getControllerAction();
                $request = $controller->getRequest();
                $response = $controller->getResponse();
                $redirectOnFailureUrl = $this->redirect->getRedirectUrl();
                $this->requestHandler->execute($key, $request, $response, $redirectOnFailureUrl);
            }
        }
    }
}
