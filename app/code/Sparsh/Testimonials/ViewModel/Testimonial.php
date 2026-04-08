<?php

namespace Sparsh\Testimonials\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Testimonial implements ArgumentInterface
{
    
     /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * General constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigData()
    {
        return $this->scopeConfig
            ->getValue('testimonial/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
   
}
