<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_OneStepCheckout
 * @copyright   Copyright (c) 2018 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
namespace Ecomteck\OneStepCheckout\Controller\Newsletter;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;

class Subscribe implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory, 
        SubscriberFactory $subscriberFactory
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        $email = $this->request->getParam('email');
        if (empty($email)) {
            $result->setData(['success' => false, 'error' => __('E-mail cannot be empty.')]);
            return $result;
        }

        try {
            /** @var Subscriber $subscriber */
            $subscriber = $this->subscriberFactory->create();
            $subscriber->subscribe($email);
            $result->setData(['success' => true]);
        } catch (\Exception $e) {
            $result->setData(['success' => false, 'error' => $e->getMessage()]);
        }
        return $result;
    }
}
