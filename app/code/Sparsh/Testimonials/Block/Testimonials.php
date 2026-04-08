<?php

namespace Sparsh\Testimonials\Block;

use Sparsh\Testimonials\Model\ModuleFactory;

class Testimonials extends \Magento\Framework\View\Element\Template
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
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Testimonials constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ModuleFactory $moduleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ModuleFactory $moduleFactory,
        array $data = []
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->timezone = $context->getLocaleDate();
        parent::__construct($context, $data);
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
     * GetTestimonialWithPaging
     *
     * @return void
     */
    public function getTestimonialWithPaging()
    {
        $today = $this->timezone->date()->format('Y-m-d 00:00:00');
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->moduleFactory->create()->getCollection();
        $collection->addFieldToFilter('status', ['eq' => "Enabled"]);
        $collection->addFieldToFilter('testimonial_date', ['lteq' => $today]);
        $collection->setOrder('sort_order', 'DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * PrepareLayout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTestimonialWithPaging()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'testimonials.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15])->setShowPerPage(true)->setCollection(
                $this->getTestimonialWithPaging()
            );
            $this->setChild('pager', $pager);
            $this->getTestimonialWithPaging()->load();
        }
        return $this;
    }
 
    /**
     * GetPagerHtml
     *
     * @return void
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
