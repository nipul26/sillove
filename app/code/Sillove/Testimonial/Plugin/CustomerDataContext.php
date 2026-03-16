<?php
namespace Sillove\Testimonial\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;

class CustomerDataContext
{
    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var Context
     */
    private Context $httpContext;

    /**
     * Constructor
     *
     * @param Session $customerSession
     * @param Context $httpContext
     */
    public function __construct(
        Session $customerSession,
        Context $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * Summary of beforeExecute
     *
     * @param \Magento\Framework\App\ActionInterface $subject
     * @return void
     */
    public function beforeExecute(ActionInterface $subject)
    {
        $this->httpContext->setValue('customer_id', $this->customerSession->getCustomerId(), false);
    }
}
