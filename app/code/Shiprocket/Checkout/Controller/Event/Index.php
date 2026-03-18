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
namespace Shiprocket\Checkout\Controller\Event;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Shiprocket\Checkout\Block\Buynow as SrBuynowBlock;
use Magento\Checkout\Model\Session as CheckoutSession;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface;

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
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Context           $context
     * @param SrBuynowBlock     $srBuynowBlock
     * @param ResultFactory     $resultFactory
     * @param CheckoutSession   $checkoutSession
     * @param ShiprocketHelper  $shiprocketHelper
     * @param Session           $customerSession
     * @param ManagerInterface  $eventManager
     */
    public function __construct(
        Context $context,
        SrBuynowBlock $srBuynowBlock,
        ResultFactory $resultFactory,
        CheckoutSession $checkoutSession,
        ShiprocketHelper  $shiprocketHelper,
        Session $customerSession,
        ManagerInterface $eventManager
    ) {
        parent::__construct($context);
        $this->srBuynowBlock = $srBuynowBlock;
        $this->context = $context;
        $this->resultFactory = $resultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->customerSession = $customerSession;
        $this->eventManager = $eventManager;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('productId');
        $childId = $this->getRequest()->getParam('childId');
        $qty = $this->getRequest()->getParam('quantity');
        $utmParams = $this->getRequest()->getParam('utmParams');
        $sessionId = $this->getRequest()->getParam('sessionId');
        $data = [
            'productId' => $productId,
            'qty' => $qty
        ];
        if ($childId) {
            $data['childId'] = $childId;
        }
        $this->eventManager->dispatch(
            'shiprocket_checkout_buynow_before',
            ['product' => $data, 'utmParams' => $utmParams, 'sessionId' => $sessionId]
        );
    }
}
