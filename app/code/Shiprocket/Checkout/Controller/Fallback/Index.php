<?php
/**
 * Shiprocket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the shiprocket.in license that is
 * available through the world-wide-web at this URL:
 * https://checkout.shiprocket.in/magento-license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Shiprocket
 * @package     Shiprocket_Checkout
 * @copyright   Copyright (c) Shiprocket (https://www.shiprocket.in/)
 * @license     https://checkout.shiprocket.in/magento-license
 */
declare(strict_types=1);
namespace Shiprocket\Checkout\Controller\Fallback;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Shiprocket\Checkout\Logger\SrLogger;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $customerSessionFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var ProductRepositoryInterface
     */
    protected $product;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSessionFactory
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $productCollectionFactory
     * @param FormKey $formKey
     * @param Cart $cart
     * @param ProductRepositoryInterface $product
     * @param SrLogger $srLogger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Session $customerSessionFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        CategoryFactory $categoryFactory,
        CollectionFactory $productCollectionFactory,
        FormKey $formKey,
        Cart $cart,
        ProductRepositoryInterface $product,
        SrLogger $srLogger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->srLogger = $srLogger;

        parent::__construct($context);
    }

    /**
     * Add action
     *
     * @return void
     */
    public function execute()
    {
        $response = ['success' => true];
        $objParam = [];
        try {
            $pid = $this->getRequest()->getParam('pid');
            $qty = ($this->getRequest()->getParam('qty')) ? $this->getRequest()->getParam('qty') : 1;
            $product = $this->product->getById($pid);
            if ($product->getTypeId() === 'configurable') {
                $cid = $this->getRequest()->getParam('cid');
                $childProduct = $this->product->getById($cid);
                $productAttributeOptions = $product->getTypeInstance(true)
                                        ->getConfigurableAttributesAsArray($product);
                $options = [];
                foreach ($productAttributeOptions as $option) {
                    $options[$option['attribute_id']] =  $childProduct->getData($option['attribute_code']);
                }
        
                $objParam = new \Magento\Framework\DataObject(
                    [
                        'product' => $product->getId(),
                        'qty' => $qty,
                        'super_attribute' => $options
                    ]
                );
                $this->cart->addProduct($product, $objParam);
            } else {
                $this->cart->addProduct($product, $qty);
            }
            $this->cart->save();
        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }

        $checkoutUrl = $this->_url->getUrl('checkout');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($checkoutUrl);
        
        return $resultRedirect;
    }
}
