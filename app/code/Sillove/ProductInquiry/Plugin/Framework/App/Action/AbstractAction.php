<?php

namespace Sillove\ProductInquiry\Plugin\Framework\App\Action;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

class AbstractAction
{
    /**
     * @var SessionFactory
     */
    private $customerSession;
    /**
     * @var Context
     */
    private $httpContext;

    /**
     * Abstract Actions Constructor
     *
     * @param SessionFactory $customerSession
     * @param Context $httpContext
     */
    public function __construct(
        SessionFactory $customerSession,
        Context $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * Around Dispatch
     *
     * @param ActionInterface $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        ActionInterface $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        $customerId = 0;
        $customerData = $this->customerSession->create();
        if ($customerData->getCustomer()->getId()) {
            $customerId = $customerData->getCustomer()->getId();
        }
        $this->httpContext->setValue(
            'context_customer_id',
            $customerId,
            ""
        );

        return $proceed($request);
    }
}
