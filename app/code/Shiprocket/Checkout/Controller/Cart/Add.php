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
namespace Shiprocket\Checkout\Controller\Cart;

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
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Shiprocket\Checkout\Logger\SrLogger;

class Add extends \Magento\Framework\App\Action\Action
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
     * @var ProductFactory
     */
    protected $product;

    /**
     * @var SrLogger
     */
    protected $logger;

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
     * @param ProductFactory $product
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
        ProductFactory $product,
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
            $products = $this->getRequest()->getParam('data');
            $productType = $this->getRequest()->getParam('type');
            if ($productType === 'grouped') {
                foreach ($products as $item) {
                    $params = [
                            'form_key' =>$this->formKey->getFormKey(),
                            'product' => $item['p'],
                            'qty'   =>$item['q'],
                    ];
                    $product = $this->product->create()->load($item['p']);
                    $this->cart->addProduct($product, $params);
                }
            } elseif ($productType === 'bundle') {
                $bundle_option = $this->getRequest()->getParam('bundle_option');
                $bundle_option_qty = $this->getRequest()->getParam('bundle_option_qty');
                $bOption = [];
                $bOptionQty = [];
                foreach ($bundle_option as $v) {
                    $bOption[$v['i']] = (int) $v['p'];
                }
                foreach ($bundle_option_qty as $v) {
                    $bOptionQty[$v['i']] = (int) $v['q'];
                }
                $params = new \Magento\Framework\DataObject(
                    [
                        'qty' => (int)$products[0]['quantity'],
                        'product' => (int)$products[0]['variantId'],
                        'bundle_option' => $bOption,
                        'bundle_option_qty' => $bOptionQty
                    ]
                );
                $product = $this->product->create()->load($products[0]['variantId']);
                $this->cart->addProduct($product, $params);
            }
            $this->cart->save();
        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);

        return $result;
    }
}
