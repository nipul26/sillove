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
use Shiprocket\Checkout\Logger\SrLogger;

class Refresh extends Action
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
        $this->srLogger = $srLogger;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $quoteId = $this->shiprocketHelper->getCheckoutSession()->getSrQuoteId();
            $NewQuoteId = $this->checkoutSession->getQuote()->getId();
            if ($quoteId == $NewQuoteId) {
                $quote = $this->cartRepository->get($quoteId);
                foreach ($quote->getAllItems() as $item) {
                    $quote->removeItem($item->getItemId());
                }
                $quote->collectTotals()->save();
                $this->cartRepository->save($quote);
                $this->checkoutSession->clearStorage();
                $this->customerSession->setCartData(null);
            }
            $sessionQuote = $this->checkoutSession->getQuote();
            foreach ($sessionQuote->getAllItems() as $item) {
                $sessionQuote->removeItem($item->getItemId());
            }
            $sessionQuote->collectTotals()->save();
            $this->cartRepository->save($sessionQuote);
            $response = ['success' => true, 'message' => __('All items were removed from the cart.')];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                ->setData($response);
    }
}
