<?php
namespace Sillove\Testimonial\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Image\AdapterFactory;
use Magento\Backend\Model\UrlInterface;
use Sillove\Testimonial\Helper\Data;
use Sillove\Testimonial\Model\TestimonialFactory;
use Sillove\Testimonial\Model\Config\Source\GridSlider;

class Testimonial extends Template implements BlockInterface
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Sillove\Testimonial\Model\TestimonialFactory
     */
    protected $testimonialFactory;

    /**
     * @var \Sillove\Testimonial\Model\Config\Source\GridSlider
     */
    protected $gridSlider;

    /**
     * @var \Sillove\Testimonial\Helper\Data
     */
    public $helper;
    
    /**
     * @var $this
     */
    protected $testimonials;
    
    /**
     * __construct
     *
     * @param Context            $context
     * @param AdapterFactory     $imageFactory
     * @param UrlInterface       $backendUrl
     * @param TestimonialFactory $testimonialFactory
     * @param GridSlider         $gridSlider
     * @param Data               $helper
     * @param array              $data
     */
    public function __construct(
        Context $context,
        AdapterFactory $imageFactory,
        UrlInterface $backendUrl,
        TestimonialFactory $testimonialFactory,
        GridSlider $gridSlider,
        Data $helper,
        array $data = []
    ) {

        $this->imageFactory      = $imageFactory;
        $this->backendUrl         = $backendUrl;
        $this->testimonialFactory = $testimonialFactory;
        $this->gridSlider         = $gridSlider;
        $this->helper             = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $data = $this->helper->getConfigModule('general');
        if ($data['slide']) {
            $data['vertical-swiping'] = $data['vertical'];
            $breakpoints = $this->getResponsiveBreakpoints();
            $responsive = '[';
            $num = count($breakpoints);
            foreach ($breakpoints as $size => $opt) {
                $item = (int) $data[$opt];
                $responsive .= '{"breakpoint": '.$size.', "settings": {"slidesToShow": '.$item.'}}';
                $num--;
                if ($num) {
                    $responsive .= ', ';
                }
            }
            $responsive .= ']';
            $data['center-mode']     = $data['center_mode'];
            $data['slides-to-show']  = $data['visible'];
            $data['autoplay-speed']  = $data['autoplay_speed'];
            $data['adaptive-height'] = $data['adaptive_height'];
            $data['swipe-to-slide']  = 'true';
            $data['responsive']      = $responsive;
        }
        $this->addData($data);
        parent::_construct();
    }
    /**
     * Get Admin URL
     *
     * @param  [type] $adminPath
     * @param  array  $routeParams
     * @param  string $storeCode
     * @return [type
     */
    public function getAdminUrl($adminPath, $routeParams = [], $storeCode = 'default')
    {
        $routeParams[] = [ '_nosid' => true, '_query' => ['___store' => $storeCode]];
        return $this->backendUrl->getUrl($adminPath, $routeParams);
    }

    /**
     * Get Testimonials
     *
     * @return array
     */
    public function getTestimonials()
    {
        if (!$this->testimonials) {
            $store = $this->_storeManager->getStore()->getStoreId();
            $testimonials = $this->testimonialFactory->create()->getCollection()
                        ->addFieldToFilter('stores', [ ['finset' => 0], ['finset' => $store]])
                        ->addFieldToFilter('status', 1);
            $testimonials->getSelect()->order(['order asc', 'testimonial_id desc']);
            $this->testimonials = $testimonials;
        }

        return $this->testimonials;
    }
    /**
     * Get Image
     *
     * @param  [type] $object
     * @return string
     */
    public function getImage($object)
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $object->getImage();
    }
    /**
     * Get Responsive BreakPoints
     *
     * @return $this
     */
    public function getResponsiveBreakpoints()
    {
        return $this->gridSlider->getBreakpoints();
    }
    /**
     * Get Side Options
     *
     * @return $this
     */
    public function getSlideOptions()
    {
        return $this->gridSlider->getSlideOptions();
    }

    /**
     * Get Frontend Config
     *
     * @return $this
     */
    public function getFrontendCfg()
    {
        if ($this->getSlide()) {
            return $this->getSlideOptions();
        }

        $this->addData(['responsive' =>json_encode($this->getGridOptions())]);
        return ['padding', 'responsive'];
    }
    /**
     * Get grid Options
     *
     * @return array
     */
    public function getGridOptions()
    {
        $options = [];
        $breakpoints = $this->getResponsiveBreakpoints();
        ksort($breakpoints);
        foreach ($breakpoints as $size => $screen) {
            $options[]= [$size-1 => $this->getData($screen)];
        }
        return $options;
    }
}
