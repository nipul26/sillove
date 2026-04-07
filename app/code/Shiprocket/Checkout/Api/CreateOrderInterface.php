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
namespace Shiprocket\Checkout\Api;

interface CreateOrderInterface
{
    /**
     * Create order
     *
     * @param mixed $orderData
     * @return mixed
     */
    public function createOrder($orderData);

    /**
     * Create order invoice
     *
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    public function createInvoice($order);

    /**
     * Create payment transaction
     *
     * @param \Magento\Sales\Model\Order $order
     * @param mixed $paymentInfo
     * @param \Shiprocket\Checkout\Model\OrderLog $srOrderLog
     * @return mixed
     */
    public function savePaymentTransaction($order, $paymentInfo, $srOrderLog);
}
