<?php
namespace Sillove\Testimonial\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Context as CustomerContext;

class Index extends Action
{
    /**
     * @var resultPageFactory
     */
    protected $resultPageFactory;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Summary of __construct
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HttpContext $httpContext
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->httpContext = $httpContext;
    }

    /**
     * Summary of execute
     */
    public function execute()
    {
        if (!$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            $this->_redirect('customer/account/login');
            return;
        }

        return $this->resultPageFactory->create();
    }
}
