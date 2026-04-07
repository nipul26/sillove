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
namespace Shiprocket\Checkout\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order;
use Magento\Framework\Controller\Result\RedirectFactory;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Shiprocket\Checkout\Model\OrderLog;
use Shiprocket\Checkout\Logger\SrLogger;

class Index extends Action
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var OrderLog
     */
    protected $srOrderLog;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param Order $order
     * @param RedirectFactory $resultRedirectFactory
     * @param PageFactory $resultPageFactory
     * @param ShiprocketHelper $shiprocketHelper
     * @param SessionFactory $sessionFactory
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param QuoteFactory $quoteFactory
     * @param CartRepositoryInterface $cartRepository
     * @param CustomerSession $customerSession
     * @param OrderLog $srOrderLog
     * @param SrLogger $srLogger
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        Order $order,
        RedirectFactory $resultRedirectFactory,
        PageFactory $resultPageFactory,
        ShiprocketHelper $shiprocketHelper,
        SessionFactory $sessionFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        QuoteFactory $quoteFactory,
        CartRepositoryInterface $cartRepository,
        CustomerSession $customerSession,
        OrderLog $srOrderLog,
        SrLogger $srLogger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->resultPageFactory = $resultPageFactory;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->sessionFactory = $sessionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->quoteFactory = $quoteFactory;
        $this->cartRepository = $cartRepository;
        $this->customerSession = $customerSession;
        $this->srOrderLog = $srOrderLog;
        $this->srLogger = $srLogger;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $oid = $this->getRequest()->getParam("oid");
        $quoteId = $this->shiprocketHelper->getCheckoutSession()->getSrQuoteId();
        $this->shiprocketHelper->getCheckoutSession()->unsSrQuoteId();
        if ($quoteId) {
            $quote = $this->checkoutSession->getQuote();
            $cQuoteId = $quote->getId();
            foreach ($quote->getAllItems() as $item) {
                $quote->removeItem($item->getItemId());
            }
            $quote->collectTotals()->save();
            $this->cartRepository->save($quote);
            $this->customerSession->setCartData(null);
            if ($cQuoteId != $quoteId) {
                $quote = $this->cartRepository->get($quoteId);
                foreach ($quote->getAllItems() as $item) {
                    $quote->removeItem($item->getItemId());
                }
                $quote->collectTotals()->save();
                $this->cartRepository->save($quote);
                $this->customerSession->setCartData(null);
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('');
        if ($oid) {
            $customerSessionId = $this->getCustomerSessionId($oid);
            $sessionId = $this->customerSession->getSessionId();
            if ($customerSessionId == $sessionId) {
                $order = $this->order->load($oid);
                $this->shiprocketHelper->getCheckoutSession()->setLastOrderId($order->getId())
                            ->setLastRealOrderId($order->getIncrementId())
                            ->setLastOrderStatus($order->getStatus())
                            ->setLastQuoteId($order->getQuoteId())
                            ->setLastSuccessQuoteId($order->getQuoteId());

                $resultRedirect->setPath('checkout/onepage/success');
                if (!$order->getCustomerIsGuest()) {
                    if (!$this->customerSession->isLoggedIn()) {
                        $customerEmail = $order->getCustomerEmail();
                        $customerId = $order->getCustomerId();
                        $websiteId = $this->storeManager->getWebsite()->getId();
                        if ($customerId) {
                            $customer = $this->customerFactory->create()->load($customerId);
                        } elseif ($customerEmail) {
                            $customer = $this->customerFactory->create()
                                ->setWebsiteId($websiteId)
                                ->loadByEmail($customerEmail);
                        }
                        $sessionManager = $this->sessionFactory->create();
                        $sessionManager->setCustomerAsLoggedIn($customer);
                    }
                }
            }
        }
    
        return $resultRedirect;
    }

    /**
     * Get customer session from order payload
     *
     * @param int $oid
     * @return string
     */
    public function getCustomerSessionId($oid)
    {
        try {
            $srOrderLog = $this->srOrderLog->load($oid, 'oid');
            $orderSessionId = '';
            if ($srOrderLog->getId()) {
                $orderData = json_decode($srOrderLog->getSrData(), true);
                $cartAttributes = [];
                if (isset($orderData['orderData']['cart_attributes'])) {
                    $cartAttributes = $orderData['orderData']['cart_attributes'];
                }
                if (!empty($cartAttributes)) {
                    foreach ($cartAttributes as $cartAttribute) {
                        if ($cartAttribute['key'] === 'session_id') {
                            $orderSessionId = $cartAttribute['value'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return $orderSessionId;
        }

        return $orderSessionId;
    }
}
