<?php

namespace Sillove\ProductInquiry\Ui\Component\Create\Form\Productinquiry;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class ProductOptions implements OptionSourceInterface
{
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var CustomerTree
     */
    protected $customerTree;
    /**
     * @var Visibility
     */
    protected $productVisibility;
    /**
     * @var Status
     */
    protected $productStatus;

    /**
     * Product Options Constructor
     *
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param RequestInterface $request
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        Status $productStatus,
        Visibility $productVisibility,
        RequestInterface $request
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->request = $request;
    }

    /**
     * Options Array
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function toOptionArray()
    {
        return $this->getCustomerTree();
    }

    /**
     * Get Customer Tree
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getCustomerTree()
    {
        if ($this->customerTree === null) {
            // get current Store Id
            $storeId = $this->_storeManager->getStore();
            $prdStatus = $this->productStatus->getVisibleStatusIds();
            // get Product Collection
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('status', ['in' => $prdStatus]);
            $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
            $collection->addStoreFilter($storeId);
            $collection->setOrder('name', 'ASC');

            foreach ($collection as $productData) {
                $productId = $productData->getEntityId();
                if (!isset($productById[$productId])) {
                    $productById[$productId] = [
                        'value' => $productId,
                    ];
                }
                $productById[$productId]['label'] = $productData->getName() . " ( " . $productData->getSku() . ")";
            }
            $this->customerTree = $productById;
        }

        return $this->customerTree;
    }
}
