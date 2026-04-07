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
namespace Shiprocket\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Shiprocket\Checkout\Logger\SrLogger;

class OrderSubmitBeforeObserver implements ObserverInterface
{
    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;
    
    /**
     * @var RequestInterface
     */
    protected $request;

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
     * @param ShiprocketHelper          $shiprocketHelper
     * @param RequestInterface          $request
     * @param CartRepositoryInterface   $cartRepository
     * @param CustomerSession           $customerSession
     * @param SrLogger                  $srLogger
     */
    public function __construct(
        ShiprocketHelper $shiprocketHelper,
        RequestInterface $request,
        CartRepositoryInterface $cartRepository,
        CustomerSession $customerSession,
        SrLogger $srLogger
    ) {
        $this->shiprocketHelper = $shiprocketHelper;
        $this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->customerSession = $customerSession;
        $this->srLogger = $srLogger;
    }

    /**
     * Sync product stock
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->shiprocketHelper->isEnabled()) {
            return false;
        }

        $order = $observer->getOrder();
        $quoteId = $order->getQuoteId();
        $quote  =   $quote = $this->cartRepository->get($quoteId);
        $codCharge =   $quote->getFastrrCodCharge();
        if ($codCharge) {
            $order->setFastrrCodCharge($codCharge);
        }
    }
}
