<?php
// @codingStandardsIgnoreFile
namespace Sillove\Testimonial\Block\Adminhtml\Helper\Renderer\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Sillove\Testimonial\Model\TestimonialFactory;

class Summary extends Template
{
    /**
     * @var TestimonialFactory
     */
    protected $testimonialFactory; //NOSONAR
    /**
     * @var string
     */
    protected $rating = null;

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
        $this->testimonialFactory = $testimonialFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get rating
     *
     * @return $this
     */
    public function getRating()
    {
        if ($this->rating != null) {
            return $this->rating;
        }
        $id = $this->getRequest()->getParam('testimonial_id');
        if ($id) {
            $storeViewId = $this->getRequest()->getParam('store');
            $model = $this->testimonialFactory->create();
            $model->setStoreViewId($storeViewId)->load($id);
            if ($model->getId()) {
                $this->rating = $model->getData('rating_summary');
            }
        }
        return $this->rating;
    }

    /**
     * Get rating to HTML
     *
     * @return $this
     */
    public function ratingHtml()
    {
        $rating = 0;
        if ($this->getRating()) {
            $rating = ceil($this->getRating() * 20);
        }
        $html = '<div class="rating-box">';
                    $html .= '<div class="rating" style="width:'. $rating .'%;"></div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Get Detailed rating
     *
     * @return $this
     */
    public function detailedHtml()
    {
        $rating = $this->getRating();
        $html = '';
        $html .= '<div class="product-review-box">
                    <table cellspacing="0" id="product-review-table">
                        <thead>
                            <tr>';
        for ($i=1; $i<=5; $i++) {
            $html .= '<th><span class="nobr">'. __("$i star") .'</span></th>';
        }
        $html .=               '</tr>
                        </thead>
                        <tbody>
                            <tr class="odd last">';
                                $class         = 'class="first"';
        for ($i=1; $i<=5; $i++) {
            $checked    = ($i == $rating) ? 'checked="checked"' : '';
            $html .= '<td '.$class.' style="width:60px;" ><input style="display:block; text-align: center; margin: 0 auto;" type="radio" name="rating_summary" id="rating_' .$i.'" value="'.$i.'" '.$checked.' /></td>';
            $class = ($i == 4) ? 'class="last"' : '';
        }
        $html .=            '</tr>
                        </tbody>
                    </table>
                </div>';
        return $html;
    }
}
