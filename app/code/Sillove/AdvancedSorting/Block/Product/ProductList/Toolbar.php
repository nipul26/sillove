<?php

namespace Sillove\AdvancedSorting\Block\Product\ProductList;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Toolbar
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{

    /**
     * Set collection to sorting option
     *
     * @param \Magento\Framework\Data\Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->_collection->setCurPage($this->getCurrentPage());

        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        $currentFullAction = $this->getRequest()->getFullActionName();

        $currentOrder = $this->getCurrentOrder();

        if ($currentOrder) {
            switch ($currentOrder) {
                case 'position':
                    $this->_collection->addAttributeToSort(
                        $currentOrder,
                        $this->getDirection('position')
                    );
                    break;
                case 'name_a_to_z':
                    $this->_collection->addAttributeToSort(
                        'name',
                        'asc'
                    );
                    break;
                case 'name_z_to_a':
                    $this->_collection->addAttributeToSort(
                        'name',
                        'desc'
                    );
                    break;
                case 'best_seller':
                    $this->_collection->getSelect()->joinLeft(
                        'sales_order_item',
                        'e.entity_id = sales_order_item.product_id',
                        ['qty_ordered'=>'SUM(sales_order_item.qty_ordered)']
                    )
                    ->group('e.entity_id')
                    ->order('qty_ordered '. $this->getDirection('best_seller'));
                    break;
                case 'top_rated':
                    $this->_collection->getSelect()->joinLeft(
                        'review_entity_summary',
                        'e.entity_id = review_entity_summary.entity_pk_value',
                        ['toprated'=>'review_entity_summary.rating_summary']
                    )
                    ->group('e.entity_id')
                    ->order('toprated '. $this->getDirection('top_rated'));
                    break;
                case 'new_arrivals':
                    $this->_collection->setOrder('created_at', $this->getDirection('new_arrivals'));
                    break;
                case 'most_viewed':
                    $this->_collection->getSelect()->joinLeft(
                        'report_viewed_product_index',
                        'e.entity_id = report_viewed_product_index.product_id',
                        ['visit_count'=>'COUNT(report_viewed_product_index.product_id)']
                    )
                    ->group('e.entity_id')
                    ->order('visit_count '. $this->getDirection('most_viewed'));
                    break;
                case 'review_count':
                    $this->_collection->getSelect()->joinLeft(
                        'review_entity_summary',
                        'e.entity_id = review_entity_summary.entity_pk_value ',
                        ['reviewcount'=>'review_entity_summary.reviews_count']
                    )
                    ->group('e.entity_id')
                    ->order('reviewcount '. $this->getDirection('review_count'));
                    break;
                case 'low_to_high_price':
                    $this->_collection->setOrder('price', 'asc');
                    break;
                case 'high_to_low_price':
                    $this->_collection->setOrder('price', 'desc');
                    break;
                default:
                    $this->_collection->setOrder($currentOrder, $this->getCurrentDirection());
                    break;
            }
        }
        return $this;
    }
    
    /**
     * Get order field
     *
     * @return null|string
     */
    protected function getOrderField()
    {
         if ($this->_orderField === null) {
            $this->_orderField = $this->_productListHelper->getDefaultSortField();
        }
        
        $currentFullAction = $this->getRequest()->getFullActionName();

        if ($currentFullAction == "catalogsearch_result_index") {
            $currentOrder = $this->_scopeConfig->getValue(
                'advanced_sorting/general/selectsort',
                ScopeInterface::SCOPE_STORE,
                null
            );

            $currentOrder = str_replace(["price/", "name/"], "", $currentOrder);

            return $currentOrder;
        }

        return $this->_orderField;
    }


    /**
     * Return Reverse direction of current direction
     *
     * @return string
     */
    public function getDirection($code)
    {
        $direction = $this->_scopeConfig->getValue(
            'advanced_sorting/'.$code.'/sorting_dir',
            ScopeInterface::SCOPE_STORE,
            null
        );

        return $direction;
    }
}
