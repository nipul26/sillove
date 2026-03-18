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
namespace Shiprocket\Checkout\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Registry;
use Magento\Checkout\Helper\Cart;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface;

class Buynow extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    
    /**
     * @var Configurable
     */
    protected $configurableProduct;
    
    /**
     * @var Product
     */
    protected $productModel;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var Cart
     */
    protected $cartHelper;
    
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Context                       $context
     * @param Registry                      $registry
     * @param CheckoutSession               $checkoutSession
     * @param Configurable                  $configurableProduct
     * @param Product                       $productModel
     * @param ProductRepository             $productRepository
     * @param Cart                          $cartHelper
     * @param ShiprocketHelper              $shiprocketHelper
     * @param StockRegistryInterface        $stockRegistry
     * @param Session                       $customerSession
     * @param ManagerInterface              $eventManager
     * @param array                         $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CheckoutSession $checkoutSession,
        Configurable $configurableProduct,
        Product $productModel,
        ProductRepository $productRepository,
        Cart $cartHelper,
        ShiprocketHelper $shiprocketHelper,
        StockRegistryInterface $stockRegistry,
        Session $customerSession,
        ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->configurableProduct = $configurableProduct;
        $this->productModel = $productModel;
        $this->productRepository = $productRepository;
        $this->cartHelper = $cartHelper;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->stockRegistry = $stockRegistry;
        $this->customerSession = $customerSession;
        $this->eventManager = $eventManager;
    }

    /**
     * Get current product
     *
     * @return object
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get cart items
     *
     * @return array
     */
    public function getCartItemsData()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $itemsData = [];
        $eventData = [];

        foreach ($items as $i => $item) {

            $product = $item->getProduct();
            $productId = $product->getId();
            $prod = $this->productRepository->getById($productId);
            $itemsData[$i] = [
                'variantId' => $productId,
                'quantity' => $item->getQty(),
                'customAttributes' => ['magentoSku' => $prod->getSku()],
                'itemId' => $item->getId(),
            ];
            $eventData[$i] = [
                'productId' => $productId,
                'quantity' => $item->getQty()
            ];

            if ($product->getTypeId() === ConfigurableType::TYPE_CODE) {
                $childId = $item->getOptionByCode('simple_product')->getProduct()->getId();
                $itemsData[$i]['variantId'] = $childId;
                $eventData[$i]['childId'] = $childId;
            }

        }

        $this->eventManager->dispatch('shiprocket_checkout_cart_before', ['products' => $itemsData]);

        return $itemsData;
    }

    /**
     * Get cart applied coupon codes
     *
     * @return string
     */
    public function getAppliedCouponCode()
    {
        $quote = $this->checkoutSession->getQuote();
        $coupons = $quote->getCouponCode();

        return $coupons;
    }

    /**
     * Display button or not
     *
     * @return boolean
     */
    public function displayShiprocketButton()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $itemsData = [];

        foreach ($items as $item) {
            $product = $item->getProduct();
            $type = $product->getTypeId();
            $itemQty = $item->getQty();
            if ($type == 'bundle') {
                return false;
            }
            $salableQty = $this->shiprocketHelper->getProductAvailableQty($product->getSku());
            if ($salableQty < $itemQty) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get product add to cart url
     *
     * @param int $productId
     * @return string
     */
    public function getAddToCartUrl($productId)
    {
        if ($productId) {
            $product = $this->productModel->load($productId);
            if ($product->getId()) {
                return $this->cartHelper->getAddUrl($product);
            }
        }
    }

    /**
     * Check if product is in stock or not
     *
     * @param string $sku
     * @param string $type
     * @return boolean
     */
    public function isProductInStock($sku, $type)
    {
        if ($sku && ($type == 'simple')) {
            return $this->shiprocketHelper->getProductAvailableQty($sku);
        }

        return true;
    }

    /**
     * Get shiprocket helper class
     *
     * @return ShiprocketHelper
     */
    public function getShiprocketHelper()
    {
        return $this->shiprocketHelper;
    }

    /**
     * Get customer session id
     *
     * @return ShiprocketHelper
     */
    public function getCustomerSessionId()
    {
        return $this->customerSession->getSessionId();
    }

    /**
     * Get customer session id
     *
     * @param sting $parentSku
     * @return ShiprocketHelper
     */
    public function getChildProducts($parentSku)
    {
        $childArray = [];
        try {
            if ($parentSku) {
                $product = $this->productRepository->get($parentSku);
                if ($product->getId()) {
                    if ($product->getTypeId() === ConfigurableType::TYPE_CODE) {
                        $childrens = $product->getTypeInstance()->getUsedProducts($product);
                        foreach ($childrens as $children) {
                            $pid = $children->getEntityId();
                            $childArray[$pid]['sku'] = $children->getSku();
                            $childArray[$pid]['name'] = $children->getName();
                            $childArray[$pid]['price'] = number_format($children->getFinalPrice(), 2);
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            return $childArray;
        }

        return $childArray;
    }
}
