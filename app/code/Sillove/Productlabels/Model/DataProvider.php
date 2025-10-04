<?php

namespace Sillove\Productlabels\Model;

use Sillove\Productlabels\Model\ResourceModel\ProductLabel\CollectionFactory as ProductLabelCollecttion;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Sillove\Productlabels\Model\ResourceModel\Discount\CollectionFactory as DiscountCollection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProductLabelCollecttion
     */
    protected $collection;
    /**
     * @var DiscountCollection
     */
    protected $discountCollection;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param StoreManagerInterface $storeManager
     * @param ProductLabelCollecttion $ProductLabelCollection
     * @param DiscountCollection $discountCollection
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        ProductLabelCollecttion $ProductLabelCollection,
        DiscountCollection $discountCollection,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $ProductLabelCollection->create();
        $this->discountCollection = $discountCollection;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Retrieve data array. It retrieves loaded data, if available,
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $Job) {
            $this->loadedData[$Job->getId()] = $Job->getData();

            if ($Job->getLabelImg() != '') {
                $label_img['label_img'][0]['name'] = $Job->getLabelImg();
                $label_img['label_img'][0]['url'] = $this->getMediaUrl() . $Job->getLabelImg();
                $fullData = $this->loadedData;
                //@codingStandardsIgnoreStart
                $this->loadedData[$Job->getId()] = array_merge($fullData[$Job->getId()], $label_img);
                //@codingStandardsIgnoreEnd
            }

            if ($Job->getRuleType() == 1) {
                $dicount_data = $this->discountCollection->create();
                $dicount_data->addFieldToFilter('label_id', ['eq' => $Job->getId()]);
                $discoun_add ['discount_row']= $dicount_data->getData();
                $fullData = $this->loadedData;
                //@codingStandardsIgnoreStart
                $this->loadedData[$Job->getId()] = array_merge($fullData[$Job->getId()], $discoun_add);
                //@codingStandardsIgnoreEnd
            }
        }
        return $this->loadedData;
    }

    /**
     * Retrieve the base media URL for the product labels.
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'ProductLabel/';
        return $mediaUrl;
    }
}
