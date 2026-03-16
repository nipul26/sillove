<?php
namespace Sillove\Testimonial\Block;

use Magento\Framework\View\Element\Template\Context;
use Sillove\Testimonial\Model\TestimonialFactory;

class Testimonial extends \Magento\Framework\View\Element\Template
{

    /**
     * @var array
     */
    protected $testimonials;

    /**
     * @var TestimonialFactory
     */
    protected $testimonialFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * __construct
     *
     * @param Context            $context
     * @param TestimonialFactory $testimonialFactory
     * @param array              $data
     */
    public function __construct(
        Context $context,
        TestimonialFactory $testimonialFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->testimonialFactory = $testimonialFactory;
    }
    
    /**
     * Get Testimonial
     *
     * @return array
     */
    public function getTestimonials()
    {
        if (!$this->testimonials) {
            $store = $this->storeManager->getStore()->getStoreId();
            $testimonials = $this->testimonialFactory->create()->getCollection()
                        ->addFieldToFilter('stores', [ ['finset' => 0], ['finset' => $store]])
                        ->addFieldToFilter('status', 1);
            $this->testimonials = $testimonials;
        }

        return $this->testimonials;
    }
}
