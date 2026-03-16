<?php
namespace Sillove\Testimonial\Model\Config\Source;

class GridSlider
{
    /**
     * Slide Options
     *
     * @return array
     */
    public function getSlideOptions()
    {
        return [
            'autoplay',
            'arrows',
            'fade',
            'center-mode',
            'adaptive-height',
            'autoplay-speed',
            'dots',
            'infinite',
            'padding',
            'vertical',
            'vertical-swiping',
            'responsive',
            'rows',
            'slides-to-show'
        ];
    }

    /**
     * BreakPoint Options
     *
     * @return array
     */
    public function getBreakpoints()
    {
        return [
            1921    =>'visible',
            1920    =>'widescreen',
            1480    =>'desktop',
            1200    =>'laptop',
            992        =>'notebook',
            768        =>'tablet',
            576        =>'landscape',
            481        =>'portrait',
            361        =>'mobile',
            1        =>'mobile'
        ];
    }
}
