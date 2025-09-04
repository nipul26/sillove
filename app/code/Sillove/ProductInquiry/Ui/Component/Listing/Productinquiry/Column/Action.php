<?php

namespace Sillove\ProductInquiry\Ui\Component\Listing\Productinquiry\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Action extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public const ROW_EDIT_URL = 'productinquiry/productinquiry/editinquiry';
    public const ROW_VIEW_URL = 'productinquiry/productinquiry/viewinquiry';
    public const ROW_DELETE_URL = 'productinquiry/productinquiry/delete';

    /**
     * Action Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
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
                if (isset($item['id'])) {
                    $deleteUrl = $this->urlBuilder->getUrl(static::ROW_DELETE_URL, ['id' => $item['id']]);
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::ROW_EDIT_URL,
                                [
                                    'id' => $item['id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => "javascript:deleteConfirm(
                            'Are you sure you want to do this row ?','"
                            . $deleteUrl . "')",
                            'label' => __('Delete'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
