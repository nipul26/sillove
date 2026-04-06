<?php

namespace Custom\CategoryMultiselect\Model\Layer\Filter\Item;

/**
 * Category filter item with multiselect URL support.
 * Builds toggle URLs (add/remove category) instead of drill-down URLs.
 */
class Category extends \Smile\ElasticsuiteCatalog\Model\Layer\Filter\Item\Category
{
    /**
     * Build URL for toggling this category in the multiselect filter.
     * Falls back to parent behavior when multiselect data is not set.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getApplyFilterValue() !== null) {
            return $this->_url->getUrl('*/*/*', [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $this->getApplyQueryStringParams(),
            ]);
        }

        return parent::getUrl();
    }

    /**
     * Build remove URL that removes only this category from multiselect,
     * rather than clearing all selected categories.
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $filter = $this->getFilter();

        if (method_exists($filter, 'getCurrentFilterValue') && !empty($filter->getCurrentFilterValue())) {
            $currentValues = $filter->getCurrentFilterValue();
            $removeValue = array_values(array_diff($currentValues, [(string) $this->getValue()]));

            if (count($removeValue) === 1) {
                $removeValue = current($removeValue);
            } elseif (empty($removeValue)) {
                $removeValue = null;
            }

            $query = [$filter->getRequestVar() => $removeValue];

            return $this->_url->getUrl('*/*/*', [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query,
                '_escape'      => true,
            ]);
        }

        return parent::getRemoveUrl();
    }

    /**
     * Append url and is_selected computed fields to the result array.
     *
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = [])
    {
        $data = parent::toArray($keys);

        if (in_array('url', $keys) || empty($keys)) {
            $data['url'] = $this->getUrl();
        }

        if (in_array('is_selected', $keys) || empty($keys)) {
            $data['is_selected'] = (bool) $this->getIsSelected();
        }

        return $data;
    }

    /**
     * Get the effective value for the query string (toggle array or single value).
     *
     * @return string|array|null
     */
    private function getApplyValue()
    {
        $value = $this->getApplyFilterValue();

        if (is_array($value) && count($value) === 1) {
            $value = current($value);
        }

        if (is_array($value) && empty($value)) {
            $value = null;
        }

        return $value;
    }

    /**
     * Build query string params for the toggle URL.
     *
     * @return array
     */
    private function getApplyQueryStringParams()
    {
        return [
            $this->getFilter()->getRequestVar()      => $this->getApplyValue(),
            $this->_htmlPagerBlock->getPageVarName()  => null,
        ];
    }
}
