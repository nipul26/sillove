<?php

namespace Custom\CategoryMultiselect\Model\Layer\Filter;

use Smile\ElasticsuiteCatalog\Model\Search\Request\Field\Mapper as RequestFieldMapper;

/**
 * Category filter with multiselect support.
 * Allows selecting multiple categories simultaneously (like attribute filters).
 */
class Category extends \Smile\ElasticsuiteVirtualCategory\Model\Layer\Filter\Category
{
    /**
     * @var array
     */
    protected $currentFilterValue = [];

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $htmlEscaper;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory                            $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                                 $storeManager
     * @param \Magento\Catalog\Model\Layer                                               $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder                       $itemDataBuilder
     * @param \Magento\Framework\Escaper                                                 $escaper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory           $dataProviderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                         $scopeConfig
     * @param \Smile\ElasticsuiteCore\Api\Search\ContextInterface                        $context
     * @param \Smile\ElasticsuiteVirtualCategory\Model\Category\Filter\Provider          $filterProvider
     * @param RequestFieldMapper                                                         $requestFieldMapper
     * @param \Smile\ElasticsuiteVirtualCategory\Model\VirtualCategory\Root              $virtualCategoryRoot
     * @param \Smile\ElasticsuiteVirtualCategory\Model\Url                               $urlModel
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                           $categoryRepository
     * @param boolean                                                                    $useUrlRewrites
     * @param array                                                                      $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $dataProviderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Smile\ElasticsuiteCore\Api\Search\ContextInterface $context,
        \Smile\ElasticsuiteVirtualCategory\Model\Category\Filter\Provider $filterProvider,
        RequestFieldMapper $requestFieldMapper,
        \Smile\ElasticsuiteVirtualCategory\Model\VirtualCategory\Root $virtualCategoryRoot,
        \Smile\ElasticsuiteVirtualCategory\Model\Url $urlModel,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        $useUrlRewrites = false,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $dataProviderFactory,
            $scopeConfig,
            $context,
            $filterProvider,
            $requestFieldMapper,
            $virtualCategoryRoot,
            $urlModel,
            $useUrlRewrites,
            $data
        );

        $this->categoryRepository = $categoryRepository;
        $this->htmlEscaper = $escaper;
    }

    /**
     * Apply category filter to the product collection.
     * Supports multiple category IDs (multiselect).
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = $request->getParam($this->_requestVar);

        if (null === $categoryId || $categoryId === '' || (is_array($categoryId) && empty($categoryId))) {
            return parent::apply($request);
        }

        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $this->currentFilterValue = array_map('strval', array_values($categoryId));

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        $productCollection->addFieldToFilter($this->getFilterField(), $this->currentFilterValue);

        $layerState = $this->getLayer()->getState();

        foreach ($this->currentFilterValue as $catId) {
            try {
                $storeId = $this->getLayer()->getCurrentStore()->getId();
                $category = $this->categoryRepository->get($catId, $storeId);
                $filter = $this->_createItem($category->getName(), $catId);
                $layerState->addFilter($filter);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $this;
    }

    /**
     * Get currently selected filter values.
     *
     * @return array
     */
    public function getCurrentFilterValue()
    {
        return $this->currentFilterValue;
    }

    /**
     * Get items data - overridden to show ALL active children categories,
     * not just those with products in the (already filtered) result set.
     * This is essential for multiselect: sibling categories must remain visible
     * so users can select/deselect them.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @return array
     */
    protected function _getItemsData()
    {
        $items = [];

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData('categories');

        $currentCategory = $this->getDataProvider()->getCategory();
        $categories = $this->getChildrenCategories();

        if ($currentCategory->getIsActive()) {
            foreach ($categories as $category) {
                if (!$category->getIsActive()) {
                    continue;
                }

                $count = 0;
                if (isset($optionsFacetedData[(int) $category->getId()])) {
                    $count = (int) $optionsFacetedData[$category->getId()]['count'];
                }

                $hasActiveFilter = !empty($this->currentFilterValue);

                if ($count > 0 || $hasActiveFilter) {
                    if ($hasActiveFilter && $count === 0) {
                        $count = (int) $category->getProductCount();
                    }

                    $items[] = [
                        'label' => $this->htmlEscaper->escapeHtml($category->getName()),
                        'value' => $category->getId(),
                        'count' => $count,
                        'url'   => $this->getCategoryFilterUrl($category),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Initialize filter items with multiselect toggle behavior.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @return $this
     */
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items = [];

        foreach ($data as $itemData) {
            $item = $this->_createItem($itemData['label'], $itemData['value'], $itemData['count']);
            $items[] = $item;

            $categoryIdStr = (string) $itemData['value'];

            if (in_array($categoryIdStr, $this->currentFilterValue, true)) {
                $item->setIsSelected(true);
                $toggleValue = array_values(array_diff($this->currentFilterValue, [$categoryIdStr]));
            } else {
                $toggleValue = array_merge($this->currentFilterValue, [$categoryIdStr]);
            }

            $item->setApplyFilterValue(array_values($toggleValue));
        }

        $this->_items = $items;

        return $this;
    }
}
