<?php

namespace Sillove\AdvancedSorting\Plugin;

use Sillove\AdvancedSorting\Model\System\SortType;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddSortOption
 */
class AddSortOption extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * StoreManager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SortType
     *
     * @var array
     */
    private $SortType;

    /**
     * AddSortOption constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param SortType              $SortType
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        SortType $SortType
    ) {
        $this->storeManager = $storeManager;
        $this->SortType = $SortType->toOptionArray();
        parent::__construct($context);
    }

    /**
     * Add sort order option created_at to frontend
     *
     * @param \Magento\Catalog\Model\Config $configmodel
     * @param array                         $options
     *
     * @return mixed
     */
    public function afterGetAttributeUsedForSortByArray($configmodel, $options)
    {
        $isEnabled = $this->scopeConfig->getValue(
            'advanced_sorting/general/enabled',
            ScopeInterface::SCOPE_STORE,
            null
        );

        if ($isEnabled) {
            $newOption = [];
            foreach ($this->SortType as $optionlist) {
                $sortOption = $this->scopeConfig->getValue(
                    'advanced_sorting/'.$optionlist['value'],
                    ScopeInterface::SCOPE_STORE,
                    null
                );

                if (isset($sortOption['enabled']) && $sortOption['enabled']) {
                    // remove "price/" or "name/" prefixes so keys are clean
                    $optionlist['value'] = str_replace(["price/", "name/"], "", $optionlist['value']);
                    $sortOption['value'] = $optionlist['value'];
                    $newOption[] = $sortOption;
                }
            }

            // sort by config ordering
            usort($newOption, function ($a, $b) {
                return $a['sorting_order'] <=> $b['sorting_order'];
            });

            // replace options array with new ones
            $options = [];
            foreach ($newOption as $option) {
                $options[$option['value']] = $option['label'];
            }
        }

        return $options;
    }
}
