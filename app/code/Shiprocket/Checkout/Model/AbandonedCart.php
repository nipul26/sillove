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
namespace Shiprocket\Checkout\Model;

use Shiprocket\Checkout\Api\AbandonedCartInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use Magento\SalesRule\Model\Rule;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;
use Magento\Framework\DB\Transaction as DBTransaction;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\SalesRule\Model\Coupon;
use Magento\Framework\Webapi\Rest\Response;
use Shiprocket\Checkout\Model\OrderLog;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Checkout\Model\Session as CheckoutSession;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\ManagerInterface;
use Shiprocket\Checkout\Logger\SrLogger;

class AbandonedCart implements AbandonedCartInterface
{
    
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var CartManagementInterface
     */
    protected $cartManagementInterface;
    
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepositoryInterface;
    
    /**
     * @var Session
     */
    protected $customerSession;
    
    /**
     * @var RegionCollection
     */
    protected $regionCollection;
    
    /**
     * @var Rule
     */
    protected $salesrule;
    
    /**
     * @var Order
     */
    protected $order;
    
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var InvoiceService
     */
    protected $invoiceService;
    
    /**
     * @var Transaction
     */
    protected $transaction;
    
    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;
    
    /**
     * @var TransactionBuilder
     */
    protected $transactionBuilder;
    
    /**
     * @var DBTransaction
     */
    protected $dbTransaction;
    
    /**
     * @var CustomerGroup
     */
    protected $customerGroup;
    
    /**
     * @var Coupon
     */
    protected $coupon;
    
    /**
     * @var Response
     */
    protected $response;
    
    /**
     * @var OrderLog
     */
    protected $srOrderLog;
    
    /**
     * @var File
     */
    protected $file;
    
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param StoreManagerInterface     $storeManager
     * @param CustomerFactory           $customerFactory
     * @param ProductRepository         $productRepository
     * @param CartManagementInterface   $cartManagementInterface
     * @param CartRepositoryInterface   $cartRepositoryInterface
     * @param Session                   $customerSession
     * @param RegionCollection          $regionCollection
     * @param Rule                      $salesrule
     * @param Order                     $order
     * @param OrderRepositoryInterface  $orderRepository
     * @param InvoiceService            $invoiceService
     * @param InvoiceSender             $invoiceSender
     * @param Transaction               $transaction
     * @param TransactionBuilder        $transactionBuilder
     * @param DBTransaction             $dbTransaction
     * @param CustomerGroup             $customerGroup
     * @param Coupon                    $coupon
     * @param Response                  $response
     * @param OrderLog                  $srOrderLog
     * @param File                      $file
     * @param CheckoutSession           $checkoutSession
     * @param ShiprocketHelper          $shiprocketHelper
     * @param QuoteFactory              $quoteFactory
     * @param ManagerInterface          $eventManager
     * @param SrLogger                  $srLogger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        ProductRepository $productRepository,
        CartManagementInterface $cartManagementInterface,
        CartRepositoryInterface $cartRepositoryInterface,
        Session $customerSession,
        RegionCollection $regionCollection,
        Rule $salesrule,
        Order $order,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction,
        TransactionBuilder $transactionBuilder,
        DBTransaction $dbTransaction,
        CustomerGroup $customerGroup,
        Coupon $coupon,
        Response $response,
        OrderLog $srOrderLog,
        File $file,
        CheckoutSession $checkoutSession,
        ShiprocketHelper $shiprocketHelper,
        QuoteFactory $quoteFactory,
        ManagerInterface $eventManager,
        SrLogger $srLogger
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->customerSession = $customerSession;
        $this->regionCollection = $regionCollection;
        $this->salesrule = $salesrule;
        $this->order = $order;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->transactionBuilder = $transactionBuilder;
        $this->dbTransaction = $dbTransaction;
        $this->customerGroup = $customerGroup;
        $this->coupon = $coupon;
        $this->response = $response;
        $this->srOrderLog = $srOrderLog;
        $this->file = $file;
        $this->checkoutSession = $checkoutSession;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->srLogger = $srLogger;
    }

    /**
     * Create abandoned cart
     *
     * @param array $cartData
     */
    public function createAbandonedCart($cartData)
    {
        $response = [
            "status" => false,
            "errors" => []
        ];
        $err = false;

        try {
            $requestData = json_decode($this->file->fileGetContents('php://input'), true);
            $cartArray = $requestData['cartData'];
            if (!isset($cartArray['email']) || $cartArray['email'] == '') {
                $response['errors'][] = 'Customer email is required!';
                $err = true;
            }
            if (!isset($cartArray['telephone']) || $cartArray['telephone'] == '') {
                $response['errors'][] = 'Customer telephone is required!';
                $err = true;
            }
            if (!isset($cartArray['firstname']) || $cartArray['firstname'] == '') {
                $response['errors'][] = 'Customer firstname is required!';
                $err = true;
            }
            if (!isset($cartArray['lastname']) || $cartArray['lastname'] == '') {
                $response['errors'][] = 'Customer lastname is required!';
                $err = true;
            }
            if (!isset($cartArray['cart_items']) || empty($cartArray['cart_items'])) {
                $response['errors'][] = 'cart items are required!';
                $err = true;
            }
            if ($err) {
                $this->sendResponse($response);
            }

        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $err = true;
            $response['errors'][] = $e->getMessage();
            $this->sendResponse($response);
        }
            
        try {
            $cartAttributes = (isset($cartArray['cart_attributes']))? $cartArray['cart_attributes'] : [];
            $cartId = null;
            $cartSessionId = null;
            $landingPageUrl = '';
            $utmParam = [];
            $utmSourceStr = '';
            if (is_array($cartAttributes)) {
                foreach ($cartAttributes as $cartAttribute) {
                    if ($cartAttribute['key'] === 'cart_id') {
                        $cartId = $cartAttribute['value'];
                    }
                    if ($cartAttribute['key'] === 'session_id') {
                        $cartSessionId = $cartAttribute['value'];
                    }
                    if ($cartAttribute['key'] === 'utm_param') {
                        $utmSourceStr = $cartAttribute['value'];
                    }
                }
            }
            if ($utmSourceStr) {
                $utmSourceArr = explode('&', $utmSourceStr);
                if (!empty($utmSourceArr)) {
                    foreach ($utmSourceArr as $utmSource) {
                        $utmArrayTemp = explode('=', $utmSource);
                        if (!empty($utmArrayTemp)) {
                            $utmKey = (isset($utmArrayTemp[0])) ? $utmArrayTemp[0] : '';
                            $utmValue = (isset($utmArrayTemp[1]))? $utmArrayTemp[1] : '';
                            $utmParam[$utmKey] = $utmValue;
                        }
                    }
                }
            }
            if ($cartId) {
                $response['status'] = true;
                $response['quote_id'] = $cartId;
            } else {
                $email = $cartArray['email'];
                $telephone = $cartArray['telephone'];
                $firstname = $cartArray['firstname'];
                $lastname = $cartArray['lastname'];
                $cartItems = $cartArray['cart_items'];

                $store = $this->storeManager->getStore();
                $websiteId = $this->storeManager->getStore()->getWebsiteId();

                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);

                if ($this->shiprocketHelper->getCustomerAttribute() == 'email') {
                    $customer->loadByEmail($email);
                    if (!$customer->getEntityId()) {
                        $customer->setWebsiteId($websiteId)
                            ->setStore($store)
                            ->setFirstname($firstname)
                            ->setLastname($lastname)
                            ->setEmail($email)
                            ->setPassword($email);
                        $customer->save();
                    }
                } else {
                    $mobAttr = $this->shiprocketHelper->getCustomerAttribute();
                    $customerData = $customer->getCollection()
                        ->addFieldToFilter($mobAttr, ['like' => '%' . $telephone])
                        ->load()
                        ->getFirstItem();
                    if ($customerData['entity_id']) {
                        $customer->loadByEmail($customerData['email']);
                        if ($customer->getEntityId()) {
                            $email = $customer->getEmail();
                        }
                    } else {
                        $customer->loadByEmail($email);
                        if (!$customer->getEntityId()) {
                            $customer->setWebsiteId($websiteId)
                                ->setStore($store)
                                ->setFirstname($firstname)
                                ->setLastname($lastname)
                                ->setEmail($email)
                                ->setPassword($email);
                            $mobPrefix = $this->shiprocketHelper->getMobilePrefix();
                            $customer->setData($mobAttr, $mobPrefix.$telephone);
                            $customer->save();
                        }
                    }
                }
                if ($customer->getEntityId()) {
                    $quote = $this->quoteFactory->create()->loadByCustomer($customer->getEntityId());
                    if (!$quote->getId()) {
                        $cartId = $this->cartManagementInterface->createEmptyCart();
                        $quote = $this->cartRepositoryInterface->get($cartId);
                    }
                    $quote->setStore($store);
                    $quote->setCustomerId($customer->getEntityId());
                    $quote->setCustomerIsGuest(false);
                    $customerGroupId = $customer->getGroupId();
                    $quote->setCustomerGroupId($customerGroupId);
                    $quote->setCustomerFirstname($firstname);
                    $quote->setCustomerLastname($lastname);
                    $quote->setCustomerEmail($email);
                    $quote->save();
                }
                $eventData = [];
        
                foreach ($cartItems as $item) {
                    if (isset($item['parent_sku']) && !empty($item['parent_sku']) &&
                    ($item['sku'] != $item['parent_sku'])) {
                        $product = $this->productRepository->get($item['parent_sku']);
                        $childProduct = $this->productRepository->get($item['sku']);
                        $eventData['productId'] = $product->getId();
                        $eventData['childId'] = $childProduct->getId();
                        $eventData['qty'] = (int) $item['quantity'];
                        $productAttributeOptions = $product->getTypeInstance(true)
                                                ->getConfigurableAttributesAsArray($product);
                        $options = [];
                        foreach ($productAttributeOptions as $option) {
                            $options[$option['attribute_id']] =  $childProduct->getData($option['attribute_code']);
                        }
                
                        $objParam = new \Magento\Framework\DataObject(
                            [
                                'product' => $product->getId(),
                                'qty' => $item['quantity'],
                                'super_attribute' => $options
                            ]
                        );
                        $quote->addProduct($product, $objParam);
                    } else {
                        $product= $this->productRepository->get($item['sku']);
                        $eventData['productId'] = $product->getId();
                        $eventData['qty'] = (int) $item['quantity'];
                        $quote->addProduct($product, (int) $item['quantity']);
                    }

                    $this->eventManager->dispatch(
                        'shiprocket_checkout_add_cart_after',
                        ['product' => $eventData, 'sessionId' => $cartSessionId, 'utmParam' => json_encode($utmParam)]
                    );
                    $quote->collectTotals();
                    $quote->save();
                }
                $this->checkoutSession->getQuote()->collectTotals()->save();
                $this->checkoutSession->setCartWasUpdated(true);
                $quote->save();

                $response['status'] = true;
                $response['quote_id'] = $quote->getId();
            }
        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $response['errors'][] = $e->getMessage();
            $this->sendResponse($response);
        }
        $this->sendResponse($response);
    }

    /**
     * Return header json response
     *
     * @param array $response
     */
    public function sendResponse($response)
    {
        $this->response->setHeader('Content-Type', 'application/json', true)
            ->setBody(json_encode($response))->sendResponse();
    }
}
