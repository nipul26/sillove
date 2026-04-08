<?php

namespace Sparsh\Testimonials\Block;

use Sparsh\Testimonials\Model\ModuleFactory;

class HomeTestimonials extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var ModuleFactory
     */
    protected $moduleFactory;
    /**
     * @var postData
     */
    private $postData = null;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var string
     */
    protected $_template = 'home_testimonial.phtml';

    /**
     * @var TimezoneInterface
     */
    protected $timezone;
    
    /**
     * ViewModel instance
     *
     * @var mixed
     */
    protected $viewModel;

    /**
     * HomeTestimonials constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ModuleFactory $moduleFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Sparsh\Testimonials\ViewModel\Testimonial $viewModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ModuleFactory $moduleFactory,
        \Magento\Framework\App\Request\Http $request,
        \Sparsh\Testimonials\ViewModel\Testimonial $viewModel,
        array $data = []
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->request = $request;
        $this->timezone = $context->getLocaleDate();
        $this->viewModel=$viewModel;
        parent::__construct($context, $data);
    }
   
    /**
     * GetTestimonialsOnHomePage
     *
     * @return void
     */
    public function getTestimonialsOnHomePage()
    {
        $today = $this->timezone->date()->format('Y-m-d 00:00:00');
        if ($this->getCurrentAction() == 'cms_index_index') {
            $collection = $this->moduleFactory->create()->getCollection()
                ->addFieldToFilter('status', ['eq' => "Enabled"])
                ->addFieldToFilter('show_on_home', ['eq' => "Yes"])
                ->addFieldToFilter('testimonial_date', ['lteq' => $today])
                ->setPageSize(10);
        } else {
            $collection = $this->moduleFactory->create()->getCollection()
                ->addFieldToFilter('status', ['eq' => "Enabled"])
                ->addFieldToFilter('testimonial_date', ['lteq' => $today])
                ->setPageSize(10);
        }
        $collection->setOrder('sort_order', 'DESC');
        $collection->setOrder('update_time', 'DESC');
        return $collection;
    }

    /**
     * GetCurrentAction
     *
     * @return void
     */
    public function getCurrentAction()
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $route      = $this->request->getRouteName();
        return $moduleName."_".$controller."_".$action; //cms_index_index
    }

    /**
     * GetMessage
     *
     * @param  mixed $msg
     * @return void
     */
    public function getMessage($msg)
    {
        if (strlen($msg) > 150) {
            $small = substr($msg, 0, 150);
            return $small." ... ";
        }
        return $msg;
    }

    /**
     * getViewModel
     *
     * @return void
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}
