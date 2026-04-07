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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Shiprocket\Checkout\Block\Buynow as SrBuynowBlock;
use Magento\Checkout\Model\Session as CheckoutSession;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\HTTP\Client\Curl;

class Index extends Action
{
    /**
     * @var SrBuynowBlock
     */
    protected $srBuynowBlock;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @param Context                       $context
     * @param SrBuynowBlock                 $srBuynowBlock
     * @param ResultFactory                 $resultFactory
     * @param CheckoutSession               $checkoutSession
     * @param ShiprocketHelper              $shiprocketHelper
     * @param ProductFactory                $productFactory
     * @param Session                       $customerSession
     * @param AddressRepositoryInterface    $addressRepository
     * @param Curl                          $curl
     */
    public function __construct(
        Context $context,
        SrBuynowBlock $srBuynowBlock,
        ResultFactory $resultFactory,
        CheckoutSession $checkoutSession,
        ShiprocketHelper  $shiprocketHelper,
        ProductFactory $productFactory,
        Session $customerSession,
        AddressRepositoryInterface $addressRepository,
        Curl $curl
    ) {
        parent::__construct($context);
        $this->srBuynowBlock = $srBuynowBlock;
        $this->context = $context;
        $this->resultFactory = $resultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->productFactory = $productFactory;
        $this->customerSession = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->curl = $curl;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $cartItems = $this->srBuynowBlock->getCartItemsData();
        $isServiceable = true;
        $stockStatus = [];
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                $cartItemId = $item['itemId'];
                unset($item['itemId']);
                $product = $this->productFactory->create()->load($item['variantId']);
                $sku = $product->getSku();
                $cartQty = $item['quantity'];
                $availableQty = $this->shiprocketHelper->getProductAvailableQty($sku);
                $stockStatus[$cartItemId]['is_serviceable'] = true;
                if ($availableQty < $cartQty) {
                    $isServiceable = false;
                    $stockStatus[$cartItemId]['is_serviceable'] = false;
                }
                $stockStatus[$cartItemId]['qty'] = $availableQty;
            }
        } else {
            $this->customerSession->setCartData(null);
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $resultJson->setHeader('Pragma', 'no-cache', true);
        $sessionId = $this->customerSession->getSessionId();
        $quoteId = $this->shiprocketHelper->getCheckoutSession()->getQuoteId();
        $utmParams = $this->shiprocketHelper->getUtmSource();
        $srUtmParams = $utmParams;
        if ($srUtmParams) {
            $srUtmParams = urlencode($srUtmParams);
        }
        if ($isServiceable) {
            $response = [
                'products' => $cartItems,
                'fallbackUrl' => $this->_url->getUrl('checkout', ['_secure' => true]),
                'cartAttributes' => ['cart_id' => $quoteId, 'session_id' => $sessionId, 'utm_param' => $utmParams],
                'utmParams' => $srUtmParams
            ];
            $coupons = $this->srBuynowBlock->getAppliedCouponCode();
            $this->shiprocketHelper->getCheckoutSession()->setSrQuoteId($quoteId);
            $resultJson->setData([
                "status" => true,
                "itemData" => $response,
                "coupons" => $coupons,
                "stockStatus" => $stockStatus
            ]);
        } else {
            $resultJson->setData(["status" => false, "stockStatus" => $stockStatus]);
        }

        return $resultJson;
    }
}
