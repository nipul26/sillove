<?php

declare(strict_types=1);

namespace Sillove\Testimonial\ViewModel;

use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerStatus implements ArgumentInterface
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * Constructor
     *
     * @param HttpContext $httpContext
     */
    public function __construct(HttpContext $httpContext)
    {
        $this->httpContext = $httpContext;
    }

    /**
     * Check if the customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
