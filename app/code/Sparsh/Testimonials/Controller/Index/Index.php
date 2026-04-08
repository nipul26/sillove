<?php
namespace Sparsh\Testimonials\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * PageFactory Instance
     *
     * @var mixed
     */
    protected $pageFactory;
    
    /**
     * ScopeConfig Instance
     *
     * @var mixed
     */
    protected $scopeConfig;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        return parent::__construct($context);
    }
    
    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getConfigData()) {
            return $this->_redirect('404notfound');
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
    
    /**
     * GetConfigData
     *
     * @return void
     */
    public function getConfigData()
    {
        return $this->scopeConfig
            ->getValue('testimonial/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
