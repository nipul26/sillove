<?php
namespace Sillove\Testimonial\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;
use Sillove\Testimonial\Model\ResourceModel\Testimonial as TestimonialModel;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as MagentoCollectionFactory;
use Magento\Store\Model\ScopeInterface;

class Testimonial extends \Magento\Framework\Model\AbstractModel
{
    /**`
     * @var ScopeConfigInterfacev
     */
    protected $scopeConfig;
    /**`
     * @var CollectionFactory
     */
    protected $testimonialCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * __construct
     *
     * @param Context                  $context
     * @param Registry                 $registry
     * @param ScopeConfigInterface     $scopeConfig
     * @param CollectionFactory        $testimonialCollectionFactory
     * @param Testimonial              $resource
     * @param Collection               $resourceCollection
     * @param MagentoCollectionFactory $productCollectionFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $testimonialCollectionFactory,
        TestimonialModel $resource,
        Collection $resourceCollection,
        MagentoCollectionFactory $productCollectionFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->testimonialCollectionFactory = $testimonialCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->scopeConfig= (object) $scopeConfig->getValue(
            'testimonial',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve post related products
     *
     * @param  int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts($storeId = null)
    {
        if (!$this->hasData('related_products')) {

            $collection = $this->productCollectionFactory->create();

            if ($storeId != null) {
                $collection->addStoreFilter($storeId);
            } elseif ($storeIds = $this->getStoreId()) {
                $collection->addStoreFilter($storeIds[0]);
            }

            $cfg = $this->scopeConfig->general;
            if (isset($cfg['attributeCode'])) {
                $collection->addAttributeToFilter($cfg['attributeCode'], $this->getOptionId());
            }

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }
}
