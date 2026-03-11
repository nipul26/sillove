<?php

namespace Sillove\AdvancedSorting\Model\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SortType
 */
class SortType implements ArrayInterface
{
    const BEST_SELLER  = "best_seller";

    const NAME_A_TO_Z = "name/name_a_to_z";
    const NAME_Z_TO_A = "name/name_z_to_a";
    const TOP_RATED = "top_rated";
    const NEW_ARRIVALS = "new_arrivals";
    const MOST_VIEWED = "most_viewed";
    const REVIEW_COUNT = "review_count";
    const LOW_TO_HIGH_PRICE = "price/low_to_high_price";
    const HIGH_TO_LOW_PRICE = "price/high_to_low_price";
    const POSITION = "position";
    const RELEVANCE = "relevance";


    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::BEST_SELLER  => __('Best Seller'),
            self::NAME_A_TO_Z => __('Name A to Z'),
            self::NAME_Z_TO_A => __('Name Z to A'),
            self::TOP_RATED => __('Top Rated'),
            self::NEW_ARRIVALS => __('New Arrivals'),
            self::LOW_TO_HIGH_PRICE => __('Low To High Price'),
            self::HIGH_TO_LOW_PRICE => __('High To Low Price'),
            self::MOST_VIEWED => __('Most Viewed'),
            self::REVIEW_COUNT => __('Review Count'),
            self::POSITION => __('Position'),
            self::RELEVANCE => __('Relevance'),

        ];
    }
}
