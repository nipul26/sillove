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

use Shiprocket\Checkout\Api\CreateOrderInterface;
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
use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Shiprocket\Checkout\Logger\SrLogger;

class CreateOrder implements CreateOrderInterface
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
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressDataFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param StoreManagerInterface         $storeManager
     * @param CustomerFactory               $customerFactory
     * @param ProductRepository             $productRepository
     * @param CartManagementInterface       $cartManagementInterface
     * @param CartRepositoryInterface       $cartRepositoryInterface
     * @param Session                       $customerSession
     * @param RegionCollection              $regionCollection
     * @param Rule                          $salesrule
     * @param Order                         $order
     * @param OrderRepositoryInterface      $orderRepository
     * @param InvoiceService                $invoiceService
     * @param InvoiceSender                 $invoiceSender
     * @param Transaction                   $transaction
     * @param TransactionBuilder            $transactionBuilder
     * @param DBTransaction                 $dbTransaction
     * @param CustomerGroup                 $customerGroup
     * @param Coupon                        $coupon
     * @param Response                      $response
     * @param OrderLog                      $srOrderLog
     * @param File                          $file
     * @param CheckoutSession               $checkoutSession
     * @param ShiprocketHelper              $shiprocketHelper
     * @param ManagerInterface              $eventManager
     * @param AddressInterfaceFactory       $addressDataFactory
     * @param AddressRepositoryInterface    $addressRepository
     * @param SrLogger                      $srLogger
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
        ManagerInterface $eventManager,
        AddressInterfaceFactory $addressDataFactory,
        AddressRepositoryInterface $addressRepository,
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
        $this->eventManager = $eventManager;
        $this->addressDataFactory = $addressDataFactory;
        $this->addressRepository = $addressRepository;
        $this->srLogger = $srLogger;
    }

    /**
     * Create order
     *
     * @param array $orderData
     */
    public function createOrder($orderData)
    {
        $response = [
            "status" => false,
            "errors" => [],
            "order_id" => '',
            "invoice_id" => '',
            "created_at" => ''
        ];
        $err = false;

        try {
            $requestData = json_decode($this->file->fileGetContents('php://input'), true);
            $orderArray = $requestData['orderData'];
            $srOrderLog = $this->srOrderLog;
            if (isset($orderArray['fastrr_order_id']) && $orderArray['fastrr_order_id'] != '') {
                $fasterOrderId = (int) $orderArray['fastrr_order_id'];
                $srOrderLog = $this->srOrderLog->load($fasterOrderId, 'sr_order_id');
                if ($srOrderLog->getEntityId() && $srOrderLog->getStatus() == 1) {
                    $response['status'] = true;
                    $response['order_id'] = (int) $srOrderLog->getOid();
                    $response['client_order_id'] = $srOrderLog->getOrderId();
                    $response['created_at'] = $srOrderLog->getCreatedAt();
                    $response['order_status_url'] = $this->storeManager->getStore()->getUrl(
                        'srcheckout/order/index',
                        ['oid' => $srOrderLog->getOid()]
                    );
                    return $this->sendResponse($response, $srOrderLog);
                }
                $isValidated = $this->validate($requestData, $srOrderLog);
            } else {
                $err = true;
                $response['errors'][] = 'Fastrr checkout order id missing!';
                return $this->sendResponse($response, $srOrderLog);
            }

        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $err = true;
            $response['errors'][] = $e->getMessage();
            return $this->sendResponse($response, $srOrderLog);
        }

        try {
            $email = $orderArray['email'];
            $mobile = $orderArray['mobile'];
            $cartItems = $orderArray['cart_items'];
            $discount = (isset($orderArray['discount']))? $orderArray['discount'] : [];
            $shippingInfo = $orderArray['shipping_info'];
            $paymentInfo = $orderArray['payment_info'];
            $cartAttributes = (isset($orderArray['cart_attributes'])) ? $orderArray['cart_attributes'] : [];
            $orderCartId = null;
            $orderSessionId = null;
            $landingPageUrl = '';
            $utmParam = [];
            $utmSourceStr = '';
            $landingUrl = '';
            if (is_array($cartAttributes)) {
                foreach ($cartAttributes as $cartAttribute) {
                    if ($cartAttribute['key'] === 'cart_id') {
                        $orderCartId = $cartAttribute['value'];
                    }
                    if ($cartAttribute['key'] === 'session_id') {
                        $orderSessionId = $cartAttribute['value'];
                    }
                    if ($cartAttribute['key'] === 'utm_param') {
                        $utmSourceStr = $cartAttribute['value'];
                    }
                    if ($cartAttribute['key'] === 'landing_page_url') {
                        $landingUrl = $cartAttribute['value'];
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
                    if (!isset($utmParam['utm_source'])) {
                        if (isset($utmParam['gbraid']) || isset($utmParam['gclid'])) {
                            $utmParam['utm_source'] = 'google';
                        }
                        if (isset($utmParam['fbclid'])) {
                            $utmParam['utm_source'] = 'facebook';
                        }
                    }
                }
            }
            if (!isset($utmParam['utm_source']) || $utmParam['utm_source'] == '') {
                $utmParam = $this->getUtmFromLandingPage($landingUrl);
            }

            $billingAddress = $orderArray['billing_address'];
            $billingAddress1 = $billingAddress['street'][0];
            $billingAddress2 = isset($billingAddress['street'][1]) ? $billingAddress['street'][1] : '';
            $billingAddress1 = strip_tags($billingAddress1);
            $billingAddress1 = str_replace(["\r", "\n"], ',', $billingAddress1);

            $billingAddress2 = strip_tags($billingAddress2);
            $billingAddress2 = str_replace(["\r", "\n"], ',', $billingAddress2);

            $billingAddress['street'][0] = $billingAddress1;
            if ($billingAddress2) {
                $billingAddress['street'][1] = $billingAddress2;
            }
            $billingRegion = $billingAddress['region'] ?? $billingAddress['state'];
            $billingRegion = preg_replace("/[^a-zA-Z]/", "", $billingRegion);
            $billingAddress['region_id'] = $this->getRegionIdByRegion(
                strtoupper($billingRegion),
                $billingAddress['country_code']
            );
            $billingAddress['country_id'] = $billingAddress['country_code'];

            $shippingAddress = $orderArray['shipping_address'];
            $shippingStreet1 = $shippingAddress['street'][0];
            $shippingStreet2 =isset($shippingAddress['street'][1]) ? $shippingAddress['street'][1] : '';
            $shippingStreet1 = strip_tags($shippingStreet1);
            $shippingStreet1 = str_replace(["\r", "\n"], ',', $shippingStreet1);

            $shippingStreet2 = strip_tags($shippingStreet2);
            $shippingStreet2 = str_replace(["\r", "\n"], ',', $shippingStreet2);
            $shippingAddress['street'][0] = $shippingStreet1;
            if ($shippingStreet2) {
                $shippingAddress['street'][1] = $shippingStreet2;
            }
            $shippingRegion = $shippingAddress['region'] ?? $shippingAddress['state'];
            $shippingRegion = preg_replace("/[^a-zA-Z]/", "", $shippingRegion);
            $shippingAddress['region_id'] = $this->getRegionIdByRegion(
                strtoupper($shippingRegion),
                $shippingAddress['country_code']
            );
            $shippingAddress['country_id'] = $shippingAddress['country_code'];

            if (isset($shippingInfo['shipping_price']) && $shippingInfo['shipping_price'] > 0) {
                $this->shiprocketHelper->getCheckoutSession()->setSrShippingPrice($shippingInfo['shipping_price']);
            } else {
                $this->shiprocketHelper->getCheckoutSession()->setSrShippingPrice(0);
            }
            if (isset($shippingInfo['cod_price']) && $shippingInfo['cod_price'] > 0) {
                $this->shiprocketHelper->getCheckoutSession()->setSrCodPrice($shippingInfo['cod_price']);
            } else {
                $this->shiprocketHelper->getCheckoutSession()->setSrCodPrice(0);
            }
            $this->shiprocketHelper->getCheckoutSession()->setSrCheckoutActive(true);
            if (isset($discount['prepaid_discount']) && $discount['prepaid_discount'] > 0) {
                $this->shiprocketHelper->getCheckoutSession()->setSrPrepaidDiscount($discount['prepaid_discount']);
            } else {
                $this->shiprocketHelper->getCheckoutSession()->setSrPrepaidDiscount(0);
            }
            $discountCouponArr = [];
            $discountCouponStr = '';
            $discountCouponAmount = 0;
            if (isset($discount['coupon_discount_list']) && !empty($discount['coupon_discount_list'])) {
                foreach ($discount['coupon_discount_list'] as $disc) {
                    $discountCouponArr[] = $disc['coupon_code'];
                    $discountCouponAmount = $discountCouponAmount + $disc['discount_amount'];
                }
                $discountCouponStr = implode(', ', $discountCouponArr);
                if ($discountCouponAmount) {
                    $this->shiprocketHelper->getCheckoutSession()->setSrDiscountCoupon($discountCouponStr);
                    $this->shiprocketHelper->getCheckoutSession()->setSrDiscountAmount($discountCouponAmount);
                } else {
                    $this->shiprocketHelper->getCheckoutSession()->setSrDiscountCoupon('');
                    $this->shiprocketHelper->getCheckoutSession()->setSrDiscountAmount(0);
                }
            } else {
                $this->shiprocketHelper->getCheckoutSession()->setSrDiscountCoupon('');
                $this->shiprocketHelper->getCheckoutSession()->setSrDiscountAmount(0);
            }

            $store = $this->storeManager->getStore();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            if ($orderCartId) {
                $isDuplicate = $this->duplicateParentExist($cartItems);
                if ($isDuplicate) {
                    $isConverted = $this->isQuoteConvertedToOrder($orderCartId);
                    if ($isConverted) {
                        $cartId = $this->cartManagementInterface->createEmptyCart();
                        $quote = $this->cartRepositoryInterface->get($cartId);
                    } else {
                        $quote = $this->cartRepositoryInterface->get($orderCartId);
                        $quote->setIsActive(true);
                        $quote->setTotalsCollectedFlag(false);
                        $quote->setCouponCode('')->collectTotals()->save();
                    }
                } else {
                    $cartId = $this->cartManagementInterface->createEmptyCart();
                    $quote = $this->cartRepositoryInterface->get($cartId);
                    $orderCartId = null;
                }
            } else {
                $cartId = $this->cartManagementInterface->createEmptyCart();
                $quote = $this->cartRepositoryInterface->get($cartId);
            }
            $quote->setStore($store);

            try {
                $customer = $this->getCustomer($email, $mobile, $billingAddress, $quote);
                if (!$customer->getId()) {
                    $customer = $this->getCustomer($email, $mobile, $billingAddress, $quote);
                }
                if (!$customer->getId()) {
                    $quote->setIsActive(false)->save();
                    $response['errors'][] = 'Customer not found!';
    
                    return $this->sendResponse($response, $srOrderLog);
                }
                $email = $customer->getEmail();
                $quote->setCustomerId($customer->getId());
                $quote->setCustomerIsGuest(false);
            } catch (\Exception $e) {
                if (is_object($quote)) {
                    $quote->setIsActive(false)->save();
                }
                $this->srLogger->critical($e->getMessage());
                $response['errors'][] = $e->getMessage();

                return $this->sendResponse($response, $srOrderLog);
            }

            $shippingFirstname = preg_replace('/[^a-zA-Z. ]/', '', $shippingAddress['firstname']);
            $shippingLastname = preg_replace('/[^a-zA-Z. ]/', '', $shippingAddress['lastname']);
            $shippingLastname = ($shippingLastname)? $shippingLastname : '.';

            $quote->setCustomerFirstname($shippingFirstname);
            $quote->setCustomerLastname($shippingLastname);
            $quote->setCustomerEmail($email);
            $quote->save();
            if (!$this->checkIfAddressExists($customer, $shippingAddress)) {
                $this->addCustomerAddress($customer, $shippingAddress);
            }
            $quote = $this->addProductToCart($quote, $cartItems, $srOrderLog, $orderCartId);

            if (is_bool($quote)) {
                return false;
            }
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $this->cartRepositoryInterface->save($quote);

            //Set Address to quote
            $quote->getBillingAddress()->addData($billingAddress);
            $quote->getBillingAddress()->save();
            $quote->getShippingAddress()->addData($shippingAddress);
            $quote->getShippingAddress()->save();
            $quote->save();

            // Collect Rates and Set Shipping & Payment Method
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                            ->collectShippingRates()
                            ->setShippingMethod('srshipping_srshipping');
            $shippingAddress->save();
            $orderStatus = 'pending';
            $orderComments = '';
            if (isset($paymentInfo['payment_type']) && $paymentInfo['payment_type'] == 'prepaid') {
                $paymentMethod = (isset($paymentInfo['payment_method'])) ? $paymentInfo['payment_method']: '';
                $quote->setPaymentMethod('srcheckout_prepaid');
                $quote->getPayment()->importData(['method' => 'srcheckout_prepaid']);
                $orderStatus = $this->shiprocketHelper->getPrepaidOrderStatus();
            } else {
                $quote->setPaymentMethod('srcheckout_cod');
                $quote->getPayment()->importData(['method' => 'srcheckout_cod']);
                $orderStatus = $this->shiprocketHelper->getCodOrderStatus();
                $orderComments = 'Order plced using COD payment';
            }
            if (isset($shippingInfo['cod_price']) && $shippingInfo['cod_price'] > 0) {
                $quote->setFastrrCodCharge($shippingInfo['cod_price']);
            }
            $quote->setInventoryProcessed(false);
            $quote->setTotalsCollectedFlag(false);
            $quote->setCouponCode('')->save();
            $quote->collectTotals();
            $this->cartRepositoryInterface->save($quote);

            // Check quote amount and paid amount
            if (isset($paymentInfo['payment_type']) && $paymentInfo['payment_type'] == 'prepaid') {
                $quoteAmount = (float) $quote->getGrandTotal();
                if (isset($paymentInfo['amount'])) {
                    $paidAmount = (float) $paymentInfo['amount'];
                    $amtDifference = abs($quoteAmount - $paidAmount);
                    if ($amtDifference >= 1) {
                        $quote->setIsActive(false)->save();
                        $response['status'] = false;
                        $response['errors'][] = "Amount Diff: Paid=>$paidAmount, Quote Amount=>$quoteAmount";
                        return $this->sendResponse($response, $srOrderLog);
                    }
                } else {
                    $quote->setIsActive(false)->save();
                    $response['status'] = false;
                    $response['errors'][] = "Captured amount data not found=>$quoteAmount";
                    return $this->sendResponse($response, $srOrderLog);
                }
            }
            // Create Order From Quote
            $quote->collectTotals();
            $this->cartRepositoryInterface->save($quote);

            $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
            $order = $this->order->load($orderId);

            // Assign order to customer if not assigned
            $billingFirstname = preg_replace('/[^a-zA-Z. ]/', '', $billingAddress['firstname']);
            $billingLastname = preg_replace('/[^a-zA-Z. ]/', '', $billingAddress['lastname']);
            $billingLastname = ($billingLastname)? $billingLastname : '.';

            $order->setCustomerId($customer->getId())
                ->setCustomerIsGuest(0)
                ->setCustomerFirstname($billingFirstname)
                ->setCustomerLastname($billingLastname)
                ->setCustomerGroupId($customer->getGroupId())
                ->save();

            // save faster order id
            $order->setFastrrOrderId($orderArray['fastrr_order_id']);
            if (isset($shippingInfo['cod_price']) && $shippingInfo['cod_price'] > 0) {
                $order->setFastrrCodCharge($shippingInfo['cod_price']);
            }

            if ($orderComments) {
                $order->addStatusHistoryComment(__($orderComments));
            }

            // set custom discount desciption to order
            $finalDiscountDescription = $discountCouponStr;
            $prepaidDiscount = isset($discount['prepaid_discount'])? $discount['prepaid_discount'] : 0;
            if ($prepaidDiscount > 0) {
                if ($discountCouponAmount > 0) {
                    $finalDiscountDescription = $finalDiscountDescription . ', Prepaid Discount';
                } else {
                    $finalDiscountDescription = 'Prepaid Discount';
                }
            }
            $order->setDiscountDescription($finalDiscountDescription);
            $order->save();
            $increment_id = $order->getRealOrderId();

            // update log table
            $srOrderLog->setStatus(1);
            $srOrderLog->setErrmsg('');
            $srOrderLog->setOrderId($increment_id);
            $srOrderLog->setOid($orderId);
            $srOrderLog->setUpdatedAt(date('Y-m-d H:i:s'));
            $srOrderLog->save();
            
            $order->setEmailSent(0);
            $response['status'] = true;
            $response['order_id'] = (int) $orderId;
            $response['client_order_id'] = $increment_id;
            $response['created_at'] = $order->getCreatedAt();
            $response['order_status_url'] = $this->storeManager->getStore()->getUrl(
                'srcheckout/order/index',
                ['oid' => $orderId]
            );
            $this->shiprocketHelper->getCheckoutSession()->setSrCheckoutComplete(true);
            // save order details in log table
        } catch (\Exception $e) {
            if (is_object($quote)) {
                $quote->setIsActive(false)->save();
            }
            $this->srLogger->critical($e->getMessage());
            $response['errors'][] = $e->getMessage();
            return $this->sendResponse($response, $srOrderLog);
        }
        // create invoice automatically based on configuration
        if ($this->shiprocketHelper->createInvoice() && $paymentInfo['payment_type'] == 'prepaid') {
            $res = $this->createInvoice($order);
            if ($res['err'] == '') {
                $response['invoice_id'] = $res['invoice_id'];
            }
        }
        if (isset($paymentInfo['transaction_id']) && $paymentInfo['transaction_id'] != '') {
            $this->savePaymentTransaction($order, $paymentInfo, $srOrderLog);
        }

        // Set order status
        if ($orderStatus != 'pending') {
            $order->setStatus($orderStatus);
            // Add a comment to the order history (optional)
            $order->addStatusHistoryComment(
                __('Order status changed to %1', $orderStatus)
            );
            $order->save();
        }

        try {
            $this->eventManager->dispatch(
                'shiprocket_checkout_order_placed',
                [
                    'order' => $order,
                    'discount' => $discount,
                    'sessionId' => $orderSessionId,
                    'utmParam' => json_encode($utmParam)
                ]
            );
        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
        }

        return $this->sendResponse($response, $srOrderLog);
    }

    /**
     * Validate order data
     *
     * @param array $requestData
     * @param OrderLog $srOrderLog
     * @return void
     */
    public function validate($requestData, $srOrderLog)
    {
        $orderArray = $requestData['orderData'];
        if ($srOrderLog->getEntityId()) {
            // success order just return the response
            if ($srOrderLog->getStatus()) {
                $orderdetails = $this->order->load($srOrderLog->getOrderId());
                $invoice_id = '';
                foreach ($orderdetails->getInvoiceCollection() as $invoice) {
                    $invoice_id = $invoice->getId();
                    break;
                }
                $response['status'] = true;
                $response['order_id'] = $orderdetails->getId();
                $response['client_order_id'] = $orderdetails->getRealOrderId();
                $response['invoice_id'] = $invoice_id;
                $response['created_at'] = $orderdetails->getCreatedAt();
                $response['order_status_url'] = $this->storeManager->getStore()->getUrl(
                    'srcheckout/order/index',
                    ['oid' => $orderdetails->getId()]
                );
                return $this->sendResponse($response, $srOrderLog);
            } else {
            // verify order in magento and return accordingly
                if ($srOrderLog->getOrderId()) {
                    $orderdetails = $this->order->load($srOrderLog->getOrderId());
                    if ($orderdetails->getId()) {
                        $invoice_id = '';
                        foreach ($orderdetails->getInvoiceCollection() as $invoice) {
                            $invoice_id = $invoice->getId();
                            break;
                        }
                        $response['status'] = true;
                        $response['order_id'] = (int) $orderdetails->getId();
                        $response['client_order_id'] = $orderdetails->getRealOrderId();
                        $response['invoice_id'] = $invoice_id;
                        $response['created_at'] = $orderdetails->getCreatedAt();
                        $response['order_status_url'] = $this->storeManager->getStore()->getUrl(
                            'srcheckout/order/index',
                            ['oid' => $orderdetails->getId()]
                        );
                        $srOrderLog->setStatus(1);
                        $srOrderLog->setUpdatedAt(date('Y-m-d H:i:s'));
                        $srOrderLog->save();
                        return $this->sendResponse($response, $srOrderLog);
                    }
                }
            }
        } else {
            if ($this->shiprocketHelper->createOrderLog()) {
                $srOrderLog->setSrData(json_encode($requestData));
            }
            $fasterOrderId = (int) $orderArray['fastrr_order_id'];
            $srOrderLog->setPaymentType($orderArray['payment_info']['payment_type']);
            $srOrderLog->setSrOrderId($fasterOrderId);
            $srOrderLog->setStatus(0);
            $srOrderLog->setCreatedAt(date('Y-m-d H:i:s'));
            $srOrderLog->save();
        }
        $err = false;
        if (!isset($orderArray['email']) || $orderArray['email'] == '') {
            $response['errors'][] = 'Customer email is required!';
            $err = true;
        }
        if (!isset($orderArray['mobile']) || $orderArray['mobile'] == '') {
            $response['errors'][] = 'Customer mobile is required!';
            $err = true;
        }
        if (!isset($orderArray['cart_items']) || empty($orderArray['cart_items'])) {
            $response['errors'][] = 'cart items are required!';
            $err = true;
        }
        if (!isset($orderArray['shipping_info']) || empty($orderArray['shipping_info'])) {
            $response['errors'][] = 'shipping details are required!';
            $err = true;
        }
        if (!isset($orderArray['payment_info']) || empty($orderArray['payment_info'])) {
            $response['errors'][] = 'payment details are required!';
            $err = true;
        }
        if (!isset($orderArray['billing_address']) || empty($orderArray['billing_address'])) {
            $response['errors'][] = 'billing address are required!';
            $err = true;
        }
        if (!isset($orderArray['shipping_address']) || empty($orderArray['shipping_address'])) {
            $response['errors'][] = 'shipping address are required!';
            $err = true;
        }
        if (!isset($orderArray['billing_address']['region']) || empty($orderArray['billing_address']['region'])) {
            if (!isset($orderArray['billing_address']['state']) || empty($orderArray['billing_address']['state'])) {
                $response['errors'][] = 'billing state code required!';
                $err = true;
            }
        }
        if (!isset($orderArray['shipping_address']['region']) || empty($orderArray['shipping_address']['region'])) {
            if (!isset($orderArray['shipping_address']['state']) || empty($orderArray['shipping_address']['state'])) {
                $response['errors'][] = 'shipping state code required!';
                $err = true;
            }
        }
        if ($err) {
            $response['status'] = false;
            return $this->sendResponse($response, $srOrderLog);
        }
    }

    /**
     * Create invoice
     *
     * @param Order $order
     */
    public function createInvoice($order)
    {
        $res = [
            'err' => ''
        ];
        try {
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                
                $transactionSave = $this->dbTransaction
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());
                $transactionSave->save();
                $this->invoiceSender->send($invoice);
                
                $order->addCommentToStatusHistory(
                    __('Notified customer about invoice creation #%1.', $invoice->getId())
                )->setIsCustomerNotified(true)->save();

                $res['invoice_id'] = $invoice->getId();
            }
        } catch (\Exception $e) {
            $this->srLogger->critical($e->getMessage());
            $res['err'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * Save payment transaction
     *
     * @param Order $order
     * @param array $paymentInfo
     * @param OrderLog $srOrderLog
     */
    public function savePaymentTransaction($order, $paymentInfo, $srOrderLog)
    {
        try {
            $paymentData = [];
            $transactionId = $paymentInfo['transaction_id'];

            $payment = $order->getPayment();
            $payment->setLastTransId($transactionId);
            $payment->setTransactionId($transactionId);
            $payment->setParentTransactionId($transactionId);

            $formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());

            $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($transactionId)
                    ->setFailSafe(true)
                    ->build(Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder($transaction, __('The captured amount is %1.', $formatedPrice));

            $payment->save();
            $order->save();
            $transaction->save();

            return true;

        } catch (Exception $e) {
            $this->srLogger->critical($e->getMessage());
            if ($srOrderLog->getEntityId()) {
                $srOrderLog->setErrmsg(json_encode($e->getMessage()));
                $srOrderLog->setUpdatedAt(date('Y-m-d H:i:s'));
                $srOrderLog->save();
            }
        }
    }

    /**
     * Get region id by region
     *
     * @param string $regionCode
     * @param string $countryCode
     */
    public function getRegionIdByRegion($regionCode, $countryCode)
    {
        $region = $this->regionCollection->addCountryFilter($countryCode);
        if ($regionCode == 'TS' || $regionCode == 'TG') {
            $region->addFieldToFilter('code', ['in' => ['TG', 'TS']]);
        } elseif ($regionCode == 'UK' || $regionCode == 'UT') {
                $region->addFieldToFilter('code', ['in' => ['UK', 'UT']]);
        } elseif ($regionCode == 'CT' || $regionCode == 'CG') {
            $region->addFieldToFilter('code', ['in' => ['CT', 'CG']]);
        } else {
            $region->addFieldToFilter('code', $regionCode);
        }
        $regionData = $region->load()->getFirstItem();
        $regionId = $regionData->getRegionId();

        return $regionId;
    }

    /**
     * Return header json response
     *
     * @param array $response
     * @param OrderLog $srOrderLog
     */
    public function sendResponse($response, $srOrderLog)
    {
        if (isset($response['errors']) && !empty($response['errors'])) {
            if ($srOrderLog->getEntityId()) {
                $srOrderLog->setErrmsg(json_encode($response['errors']));
                $srOrderLog->setUpdatedAt(date('Y-m-d H:i:s'));
                $srOrderLog->save();
            }
        }
        $this->response->setHeader('Content-Type', 'application/json', true)
            ->setBody(json_encode($response))->sendResponse();
        return false;
    }
    /**
     * Check if a customer already has the given address.
     *
     * @param object $customer Customer
     * @param array $newAddr New Address
     * @return bool
     */
    public function checkIfAddressExists($customer, $newAddr)
    {
        $addressCollection = $customer->getAddresses();
        foreach ($addressCollection as $address) {
            if ($address->getStreet() == $newAddr['street']
                && $address->getCity() === $newAddr['city']
                && $address->getPostcode() === $newAddr['postcode']
                && $address->getCountryId() === $newAddr['country_id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save address to customer address if not exist.
     *
     * @param object $customer
     * @param array $newAddr
     * @return bool
     */
    public function addCustomerAddress($customer, $newAddr)
    {
        $address = $this->addressDataFactory->create();
        $mobPrefix = $this->shiprocketHelper->getMobilePrefix();
        $setDefaultAddress = $this->shiprocketHelper->getDefaultAddressSetting();
        $firstname = preg_replace('/[^a-zA-Z. ]/', '', $newAddr['firstname']);
        $lastname = preg_replace('/[^a-zA-Z. ]/', '', $newAddr['lastname']);
        $lastname = ($lastname)? $lastname : '.';

        $address->setCustomerId($customer->getId())
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setStreet($newAddr['street'])
            ->setCity($newAddr['city'])
            ->setRegionId($newAddr['region_id'])
            ->setPostcode($newAddr['postcode'])
            ->setCountryId($newAddr['country_id'])
            ->setTelephone($mobPrefix.$newAddr['telephone']);
        if ($setDefaultAddress) {
            $address->setIsDefaultShipping(true)
            ->setIsDefaultBilling(true);
        }
        try {
            $this->addressRepository->save($address);
        } catch (\Exception $e) {
            $this->srLogger->critical("Error adding address: " . $e->getMessage());
        }
    }

    /**
     * Add product into cart.
     *
     * @param object $quote
     * @param array $cartItems
     * @param object $srOrderLog
     * @param int $orderCartId
     * @return object|boolean $quote
     */
    public function addProductToCart($quote, $cartItems, $srOrderLog, $orderCartId)
    {
        try {
            if ($orderCartId) {
                $removeStatus = $this->removeNonMatchingCartItems($quote, $cartItems);
            }
            foreach ($cartItems as $item) {
                $parentSku = $this->getParentSku($item);
                if (isset($item['parent_sku']) && !empty($item['parent_sku']) &&
                ($item['sku'] != $parentSku)) {
                    $product = $this->productRepository->get($parentSku);
                    if ($product->getTypeId() != 'configurable') {
                        $response['status'] = false;
                        $response['errors'][] = "Invalid parent sku: $parentSku";
                        $this->sendResponse($response, $srOrderLog);
                        return false;
                    }
                    $productId = $product->getId();
                    $childProduct = $this->productRepository->get($item['sku']);
                    $productAttributeOptions = $product->getTypeInstance(true)
                                            ->getConfigurableAttributesAsArray($product);
                    $options = [];
                    foreach ($productAttributeOptions as $option) {
                        $options[$option['attribute_id']] =  $childProduct->getData($option['attribute_code']);
                    }
                    $requiredQty = $item['quantity'];
                    $itemExist = $this->checkProductInCartExistsWithQty($quote, $productId, $requiredQty, $options);
                    if (!$itemExist) {
                        $objParam = new \Magento\Framework\DataObject(
                            [
                                'product' => $product->getId(),
                                'item' => $product->getId(),
                                'qty' => $requiredQty,
                                'super_attribute' => $options
                            ]
                        );
                        $quote->addProduct($product, $objParam);
                    }
                } else {
                    $product = $this->productRepository->get($item['sku']);
                    $productId = $product->getId();
                    $requiredQty = (int) $item['quantity'];
                    $itemExist = $this->checkProductInCartExistsWithQty($quote, $productId, $requiredQty);
                    if (!$itemExist) {
                        $quote->addProduct($product, $requiredQty);
                    }
                }
            }
            $quote->save();

            return $quote;
        } catch (\Exception $e) {
            if (is_object($quote)) {
                $quote->setIsActive(false)->save();
            }
            $this->srLogger->critical('Product Add Error: '.$e->getMessage());
            $response['errors'][] = $e->getMessage();
            $this->sendResponse($response, $srOrderLog);

            return false;
        }
    }

    /**
     * Check product exist in cart.
     *
     * @param object $quote
     * @param int $productId
     * @param int $requiredQty
     * @param array $superAttributes
     */
    public function checkProductInCartExistsWithQty($quote, $productId, $requiredQty, $superAttributes = [])
    {
        $cartItems = $quote->getAllVisibleItems();
    
        foreach ($cartItems as $item) {
            $qty = $item->getQty();
            $updateQty = false;
            if ($item->getProductId() == $productId) {
                if ($item->getProductType() === 'configurable' && !empty($superAttributes)) {
                    $options = $item->getProduct()->getCustomOption('attributes');
                    if ($options) {
                        $cartAttributes = json_decode($options->getValue(), true);
                        if ($cartAttributes == $superAttributes) {
                            $updateQty = true;
                        }
                    }
                } else {
                    $updateQty = true;
                }
                if ($updateQty) {
                    $item->setQty($requiredQty);
                    $quote->save();
                }
            }
        }
        return false;
    }
    
    /**
     * Remove non matching products from cart.
     *
     * @param object $quote
     * @param array $items
     */
    public function removeNonMatchingCartItems($quote, $items)
    {
        try {
            $cartItems = $quote->getAllVisibleItems();
    
            foreach ($cartItems as $cartItem) {
                $itemExist = false;
                foreach ($items as $item) {
                    if ($cartItem->getProductType() === 'configurable') {
                        $isExist = $this->isConfigProductExistInCart($cartItem, $item);
                        if ($isExist) {
                            $itemExist = true;
                        }
                    } else {
                        $product = $this->productRepository->get($item['sku']);
                        $productId = $product->getId();
                        if ($cartItem->getProductId() === $productId) {
                            $itemExist = true;
                        }
                    }
                }
                if (!$itemExist) {
                    $quote->removeItem($cartItem->getId());
                }
            }
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $this->cartRepositoryInterface->save($quote);
    
            return true;
        } catch (\Exception $e) {
            return false;
        }
    
        return false;
    }

    /**
     * Check product exist in cart.
     *
     * @param object $cartItem
     * @param array $item
     */
    public function isConfigProductExistInCart($cartItem, $item)
    {
        $itemExist = false;
        $parentSku = $this->getParentSku($item);
        if (isset($item['parent_sku']) && !empty($item['parent_sku'])
        && ($item['sku'] != $parentSku)) {
            $product = $this->productRepository->get($parentSku);
            $productId = $product->getId();
            if ($cartItem->getProductId() === $productId) {
                $childProduct = $this->productRepository->get($item['sku']);
                $productAttributeOptions = $product->getTypeInstance(true)
                    ->getConfigurableAttributesAsArray($product);
                $superAttributes = [];
                $requiredQty = $item['quantity'];
                foreach ($productAttributeOptions as $option) {
                    $superAttributes[$option['attribute_id']] =
                        $childProduct->getData($option['attribute_code']);
                }
                $options = $cartItem->getProduct()->getCustomOption('attributes');
                if ($options) {
                    $cartAttributes = json_decode($options->getValue(), true);
                    if ($cartAttributes === $superAttributes) {
                        $itemExist = true;
                    }
                }
            }
        }

        return $itemExist;
    }

    /**
     * Check if quote converted into order.
     *
     * @param int $quoteId
     */
    public function isQuoteConvertedToOrder($quoteId)
    {
        try {
            $orderCollection = $this->order->getCollection()
                ->addFieldToFilter('quote_id', $quoteId);
    
            return $orderCollection->getSize();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get customer.
     *
     * @param sting $email
     * @param sting $mobile
     * @param array $billingAddress
     * @param object $quote
     */
    public function getCustomer($email, $mobile, $billingAddress, $quote)
    {
        $store = $this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $firstname = preg_replace('/[^a-zA-Z. ]/', '', $billingAddress['firstname']);
        $lastname = preg_replace('/[^a-zA-Z. ]/', '', $billingAddress['lastname']);
        $lastname = ($lastname)? $lastname : '.';

        try {
            $customer->setWebsiteId($websiteId);
            $mobPrefix = $this->shiprocketHelper->getMobilePrefix();
            if ($this->shiprocketHelper->getOrderAsGuest()) {
                $quote->setCustomerId(null);
                $quote->setCustomerIsGuest(true);
            } else {
                if ($this->shiprocketHelper->getCustomerAttribute() == 'email') {
                    $customer->loadByEmail($email);
                    if (!$customer->getId()) {
                        $customer->setWebsiteId($websiteId)
                            ->setStore($store)
                            ->setFirstname($firstname)
                            ->setLastname($lastname)
                            ->setEmail($email)
                            ->setPassword($email);
                        $customer->save();
                    }
                    if ($customer->getId()) {
                        $quote->setCustomerId($customer->getId())
                            ->setCustomerEmail($customer->getEmail())
                            ->setCustomerIsGuest(false)
                            ->setFirstname($firstname)
                            ->setLastname($lastname)
                            ->setStoreId($store->getId())
                            ->save();
                    }
                } else {
                    $mobAttr = $this->shiprocketHelper->getCustomerAttribute();
                    $customerData = $customer->getCollection()
                        ->addFieldToFilter($mobAttr, [['like' => $mobile], ['like' => $mobPrefix.$mobile]])
                        ->load()
                        ->getFirstItem();
                    if ($customerData['entity_id']) {
                        $customer->loadByEmail($customerData['email']);
                        if ($customer->getId()) {
                            $quote->setCustomerId($customer->getId())
                            ->setCustomerEmail($customer->getEmail())
                            ->setCustomerIsGuest(false)
                            ->setFirstname($firstname)
                            ->setLastname($lastname)
                            ->setStoreId($store->getId())
                            ->save();
                        }
                    } else {
                        $customer->loadByEmail($email);
                        if (!$customer->getId()) {
                            $customer->setWebsiteId($websiteId)
                                ->setStore($store)
                                ->setFirstname($billingAddress['firstname'])
                                ->setLastname($billingAddress['lastname'])
                                ->setEmail($email)
                                ->setPassword($email);
                            $customer->setData($mobAttr, $mobPrefix.$mobile);
                            $customer->save();
                        }
                        if ($customer->getId()) {
                            $quote->setCustomerId($customer->getId());
                            $quote->setCustomerIsGuest(false);
                            $quote->save();
                        }
                    }
                }
                if ($customer->getId()) {
                    $customer->setMobile($mobPrefix.$mobile);
                    $customer->setTelephone($mobPrefix.$mobile);
                    $customer->save();
                }
                $customerGroupId = $customer->getGroupId();
                $quote->setCustomerGroupId($customerGroupId);
                $quote->save();
            }
        } catch (\Exception $e) {
            $this->srLogger->info(($e->getMessage()));
        }

        return $customer;
    }

    /**
     * Get utm params from landing page url.
     *
     * @param sting $url
     */
    public function getUtmFromLandingPage($url)
    {
        $params = [];
        if ($url) {
            $pos = strpos($url, '?');
            if ($pos === false) {
                return $params;
            }
            $queryString = substr($url, $pos + 1);
            $paramsArray = explode('&', $queryString);
            $params = [];
            foreach ($paramsArray as $param) {
                $pair = explode('=', $param, 2);
                if (count($pair) === 2) {
                    $key = urldecode($pair[0]);
                    $value = urldecode($pair[1]);
                    $params[$key] = $value;
                }
            }
        }

        if (!isset($params['utm_source'])) {
            if (isset($params['gbraid']) || isset($params['gclid'])) {
                $params['utm_source'] = 'google';
            }
            if (isset($params['fbclid'])) {
                $params['utm_source'] = 'facebook';
            }
        }

        return $params;
    }

    /**
     * Get parent sku of item.
     *
     * @param array $item
     */
    public function getParentSku($item)
    {
        $parentSku = $item['parent_sku'];
        $itemAttributes = isset($item['item_attributes']) ?  $item['item_attributes'] : [];
        if (is_array($itemAttributes) && !empty($itemAttributes)) {
            foreach ($itemAttributes as $itemAttribute) {
                if ($itemAttribute['key'] == 'magentoSku') {
                    $parentSku = $itemAttribute['value'];
                }
            }
        }

        return $parentSku;
    }

    /**
     * Check if duplicate parent exist
     *
     * @param array $cartItems
     */
    public function duplicateParentExist($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $pSku = $this->getParentSku($cartItem);
            if (!empty($pSkusArr)) {
                if (in_array($pSku, $pSkusArr)) {
                    return true;
                }
            }
            $pSkusArr[] = $pSku;
        }

        return false;
    }
}
