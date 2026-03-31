<?php

namespace Codezspark\CouponList\Block\Cart;

use Codezspark\CouponList\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Checkout\Block\Cart\Coupon;

class CouponList extends Coupon
{
    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * Constructor.
     *
     * @param Data $helperData
     * @param Context $context
     * @param CollectionFactory $ruleCollectionFactory
     * @param Cart $cart
     * @param CartRepositoryInterface $quoteRepository
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param TimezoneInterface $timeZone
     * @param array $data
     */
    public function __construct(
        Data $helperData,
        Context $context,
        CollectionFactory $ruleCollectionFactory,
        Cart $cart,
        CartRepositoryInterface $quoteRepository,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        TimezoneInterface $timeZone,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helperData = $helperData;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->timeZone = $timeZone;
    }

    /**
     * Get the helper data object.
     *
     * @return Data
     */
    public function getListHelper()
    {
        return $this->helperData;
    }

    /**
     * Get the IDs of applied cart price rules.
     *
     * @return array
     */
    public function getAppliedRules()
    {
        $cartQuote = $this->cart->getQuote();

        return $cartQuote->getAppliedRuleIds() ? explode(',', $cartQuote->getAppliedRuleIds()) : [];
    }

    /**
     * Get the list of coupons applicable to the current cart.
     *
     * @return array
     */
    public function getCouponList()
    {
        $rules = $this->ruleCollectionFactory->create();
        $ruleArray = [];
        $cartQuote = $this->cart->getQuote();
        if ($cartQuote->getId()) {
            $quotesData = $this->quoteRepository->get($cartQuote->getId());
            $productCount = 0;
            foreach ($quotesData->getAllItems() as $quotesDataKey => $quotesDataVal) {
                $address = $quotesDataVal->getAddress();
                $productCount++;
                break;
            }
            $validRuleIds = [];
            if ($productCount) {
                $address->setCachedItemsAll($quotesData->getAllItems());
                $ruleCollections = $this->ruleCollectionFactory->create()
                                        ->addCustomerGroupFilter($this->getListHelper()->getGroupId());

                foreach ($ruleCollections as $rule) {
                    if ($rule->getConditions()->validate($address)) {
                        $validRuleIds[] = $rule->getId();
                    }
                }
            }

            $showCoupons = $this->getListHelper()->getShowCouponsConfig();
            $ruleArray = [];
            if ($showCoupons == 'valid') {
                foreach ($rules as $rule) {
                    if ((in_array($rule->getId(), $validRuleIds)) &&
                        (in_array($this->getListHelper()->getWebsiteId(), $rule->getWebsiteIds())) &&
                        ($rule->getCode()) &&
                        ($rule->getShowInCouponList()) &&
                        ($rule->getIsActive()) &&
                        (($rule->getFromDate()=='') ||
                        ($rule->getFromDate() <= $this->getCurrentDate())) &&
                        (($rule->getToDate()=='') ||
                        ($rule->getToDate() >= $this->getCurrentDate()))) {

                        if (in_array($rule->getId(), $this->getAppliedRules())) {
                            $ruleArray[] = [
                                'valid' => 1,
                                'rule_id' => $rule->getId(),
                                'name' => $rule->getName(),
                                'description' => $rule->getDescription(),
                                'coupon' => $rule->getCode(),
                                'applied' => 1
                            ];
                        } else {
                            $ruleArray[] = [
                                'valid' => 1,
                                'rule_id' => $rule->getId(),
                                'name' => $rule->getName(),
                                'description' => $rule->getDescription(),
                                'coupon' => $rule->getCode(),
                                'applied' => 0
                            ];
                        }
                    }
                }
            } else {
                foreach ($rules as $rule) {
                    if (($rule->getCode()) &&
                        (in_array($this->getListHelper()->getWebsiteId(), $rule->getWebsiteIds())) &&
                        ($rule->getShowInCouponList()) &&
                        ($rule->getIsActive()) &&
                        (($rule->getFromDate()=='') ||
                        ($rule->getFromDate() <= $this->getCurrentDate())) &&
                        (($rule->getToDate()=='') ||
                        ($rule->getToDate() >= $this->getCurrentDate()))) {

                        if (in_array($rule->getId(), $validRuleIds)) {
                            if (in_array($rule->getId(), $this->getAppliedRules())) {
                                $ruleArray[] = [
                                    'valid' => 1,
                                    'rule_id' => $rule->getId(),
                                    'name' => $rule->getName(),
                                    'description' => $rule->getDescription(),
                                    'coupon' => $rule->getCode(),
                                    'applied' => 1
                                ];
                            } else {
                                $ruleArray[] = [
                                    'valid' => 1,
                                    'rule_id' => $rule->getId(),
                                    'name' => $rule->getName(),
                                    'description' => $rule->getDescription(),
                                    'coupon' => $rule->getCode(),
                                    'applied' => 0
                                ];
                            }
                        } else {
                            $ruleArray[] = [
                                'valid' => 0,
                                'rule_id' => $rule->getId(),
                                'name' => $rule->getName(),
                                'description' => $rule->getDescription(),
                                'coupon' => $rule->getCode(),
                                'applied' => 0
                            ];
                        }
                    }
                }
            }
            usort($ruleArray, function ($first, $second) {
                return strcmp($second['valid'], $first['valid']);
            });
        }
        return $ruleArray;
    }

    /**
     * Get the current date formatted as 'Y-m-d'.
     *
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->timeZone->date()->format('Y-m-d');
    }
}
