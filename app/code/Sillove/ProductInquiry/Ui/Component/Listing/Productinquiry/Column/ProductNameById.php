<?php

namespace Sillove\ProductInquiry\Ui\Component\Listing\Productinquiry\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\ProductFactory;

class ProductNameById extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var ProductFactory
     */
    protected $product;

    /**
     * Product Name By Id
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $product
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ProductFactory $product,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->product = $product;
    }

    /**
     * Prepare DataSource
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['prd_entity_id'])) {
                    $prdId = $item['prd_entity_id'];
                    $product_collection = $this->product->create()->load($prdId);
                    $item['prd_entity_id'] = $product_collection->getName();
                }
            }
        }

        return $dataSource;
    }
}
