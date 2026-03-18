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

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Webapi\Rest\Response;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Logger\SrLogger;

class SrOrderCount
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param CustomerSession           $customerSession
     * @param OrderRepositoryInterface  $orderRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param StoreManagerInterface     $storeManager
     * @param CustomerFactory           $customerFactory
     * @param Response                  $response
     * @param ShiprocketHelper          $shiprocketHelper
     * @param SrLogger                  $srLogger
     */
    public function __construct(
        CustomerSession $customerSession,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        Response $response,
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->customerSession = $customerSession;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->response = $response;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->srLogger = $srLogger;
    }

    /**
     * Get customer order count
     *
     * @param string $phone
     * @return array
     */
    public function getOrderCountByCustomerId($phone)
    {
        $response = [
            'status' => false,
            'count' => 0
        ];
        $customerId = $this->getCustomer($phone);
        if ($customerId) {
            $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $customerId, 'eq')
            ->create();

            $orderList = $this->orderRepository->getList($searchCriteria);
            $response['status'] = true;
            $response['count'] = $orderList->getTotalCount();
        }

        $this->response->setHeader('Content-Type', 'application/json', true)
            ->setBody(json_encode($response))->sendResponse();
    }

    /**
     * Get customer.
     *
     * @param sting $mobile
     * @param sting $email
     */
    public function getCustomer($mobile, $email = '')
    {
        $store = $this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        try {
            $customer->setWebsiteId($websiteId);
            $mobPrefix = $this->shiprocketHelper->getMobilePrefix();
            if ($this->shiprocketHelper->getCustomerAttribute() == 'email') {
                if ($email) {
                    $customer->loadByEmail($email);
                    if ($customer->getId()) {
                        return $customer->getId();
                    }
                }
            } else {
                $mobAttr = $this->shiprocketHelper->getCustomerAttribute();
                $customerData = $customer->getCollection()
                    ->addFieldToFilter($mobAttr, ['like' => '%' . $mobile])
                    ->load()
                    ->getFirstItem();
                if ($customerData['entity_id']) {
                    $customer->loadByEmail($customerData['email']);
                    if ($customer->getId()) {
                        return $customer->getId();
                    }
                } else {
                    if ($email) {
                        $customer->loadByEmail($email);
                        if ($customer->getId()) {
                            return $customer->getId();
                        }
                    }
                }
            }
            $customer->getId();
        } catch (\Exception $e) {
            $this->srLogger->info(($e->getMessage()));
        }

        return null;
    }
}
