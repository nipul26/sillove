<?php

namespace Sillove\ProductInquiry\Model;

use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry\Collection;
use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var LoadedData
     */
    protected $loadedData;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ProductFactory
     */
    protected $product;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Data Provider Construct
     *
     * @param ProductFactory $product
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $ProductInquiryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        ProductFactory $product,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $ProductInquiryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->product = $product;
        $this->coreRegistry = $coreRegistry;
        $this->collection = $ProductInquiryCollectionFactory->create();
        $this->_storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Get Data
     *
     * @return LoadedData
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
            /* file attachament */
            if ($model->getAttechmentFile()) {
                $m['attechment_file'][0]['name'] = $model->getAttechmentFile();
                $m['attechment_file'][0]['url'] = $this->getMediaUrl() . $model->getAttechmentFile();
                $fullData = $this->loadedData;
                //@codingStandardsIgnoreStart
                $this->loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $m);
                //@codingStandardsIgnoreEnd
            }
        }

        $unsetData = $this->dataPersistor->get('inquiry_data');

        if (!empty($unsetData)) {
            $blanckItems = $this->collection->getNewEmptyItem();
            $blanckItems->setData($unsetData);
            $this->loadedData[$blanckItems->getId()] = $blanckItems->getData();
            $this->dataPersistor->clear('inquiry_data');
        }

        return $this->loadedData;
    }

    /**
     * Get Media Url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'Product_Inquiry_Attachments/';
        return $mediaUrl;
    }
}
