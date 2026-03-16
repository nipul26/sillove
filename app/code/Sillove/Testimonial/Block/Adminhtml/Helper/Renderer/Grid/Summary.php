<?php
// phpcs:disable Generic.Files.LineLength.TooLong
namespace Sillove\Testimonial\Block\Adminhtml\Helper\Renderer\Grid;

use Magento\Backend\Block\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Sillove\Testimonial\Model\TestimonialFactory;

class Summary extends AbstractRenderer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager; //NOSONAR

    /**
     * @var \Magestore\Testimonial\Model\TestimonialFactory
     */
    protected $testimonialFactory; //NOSONAR

    /**
     * __construct
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param TestimonialFactory    $testimonialFactory
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TestimonialFactory $testimonialFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->testimonialFactory  = $testimonialFactory;
    }
    
    /**
     * @inheritdoc
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '<div class="field-summary_rating"><span class="rating-box" style="display:block;">';
        $html .= '<span style="display:block; width:'. $row->getData($this->getColumn()->getIndex()) * 20 .'%;" class="rating"></span>';
        $html .= '</span></div>';
        return $html;
    }
}
