<?php
namespace Codezspark\CouponList\Model;

use Codezspark\CouponList\Api\CouponManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Codezspark\CouponList\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Webapi\Rest\Response;

class CouponManagement implements CouponManagementInterface
{
    protected $ruleCollectionFactory;
    protected $quoteRepository;
    protected $cart;
    protected $helper;
    protected $timeZone;
    protected $response;

    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        CartRepositoryInterface $quoteRepository,
        Cart $cart,
        Data $helper,
        TimezoneInterface $timeZone,
        Response $response
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->cart = $cart;
        $this->helper = $helper;
        $this->timeZone = $timeZone;
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getApplicableCouponCodes($quote_id)
    {
        $response = [
            'status' => false,
            'message' => '',
            'data' => []
        ];

        try {
            if (!$this->helper->getModuleStatusConfig()) {
                $response['message'] = __('Module is disabled in configuration.');
                return $this->response->setBody(json_encode($response))->sendResponse();
            }

            if (!$quote_id) {
                throw new LocalizedException(__('Quote ID is required.'));
            }

            $quote = $this->quoteRepository->get($quote_id);
            $rules = $this->ruleCollectionFactory->create();

            $productCount = 0;
            foreach ($quote->getAllItems() as $item) {
                $address = $item->getAddress();
                $productCount++;
                break;
            }

            $validRuleIds = [];
            if ($productCount) {
                $address->setCachedItemsAll($quote->getAllItems());
                $ruleCollections = $this->ruleCollectionFactory->create()
                    ->addCustomerGroupFilter($quote->getCustomerGroupId());

                foreach ($ruleCollections as $rule) {
                    if ($rule->getConditions()->validate($address)) {
                        $validRuleIds[] = $rule->getId();
                    }
                }
            }

            $showCoupons = $this->helper->getShowCouponsConfig();
            $ruleArray = [];

            if ($showCoupons == 'valid') {
                foreach ($rules as $rule) {
                    if (
                        in_array($rule->getId(), $validRuleIds) &&
                        in_array($quote->getStore()->getWebsiteId(), $rule->getWebsiteIds()) &&
                        $rule->getCode() &&
                        $rule->getShowInCouponList() &&
                        $rule->getIsActive() &&
                        (($rule->getFromDate() == '') || ($rule->getFromDate() <= $this->getCurrentDate())) &&
                        (($rule->getToDate() == '') || ($rule->getToDate() >= $this->getCurrentDate()))
                    ) {
                        $ruleArray[] = [
                            'valid' => 1,
                            'rule_id' => $rule->getId(),
                            'name' => $rule->getName(),
                            'description' => $rule->getDescription(),
                            'coupon' => $rule->getCode(),
                            'applied' => in_array($rule->getId(), $this->getAppliedRules($quote)) ? 1 : 0
                        ];
                    }
                }
            } else {
                foreach ($rules as $rule) {
                    if (
                        $rule->getCode() &&
                        in_array($quote->getStore()->getWebsiteId(), $rule->getWebsiteIds()) &&
                        $rule->getShowInCouponList() &&
                        $rule->getIsActive() &&
                        (($rule->getFromDate() == '') || ($rule->getFromDate() <= $this->getCurrentDate())) &&
                        (($rule->getToDate() == '') || ($rule->getToDate() >= $this->getCurrentDate()))
                    ) {
                        $isValid = in_array($rule->getId(), $validRuleIds);
                        $ruleArray[] = [
                            'valid' => $isValid ? 1 : 0,
                            'rule_id' => $rule->getId(),
                            'name' => $rule->getName(),
                            'description' => $rule->getDescription(),
                            'coupon' => $rule->getCode(),
                            'applied' => in_array($rule->getId(), $this->getAppliedRules($quote)) ? 1 : 0
                        ];
                    }
                }
            }

            usort($ruleArray, function ($first, $second) {
                return strcmp($second['valid'], $first['valid']);
            });

            if (count($ruleArray)) {
                $response['status'] = true;
                $response['message'] = __('Applicable coupon list retrieved successfully.');
                $response['data'] = $ruleArray;
            } else {
                $response['status'] = true;
                $response['message'] = __('No applicable coupon codes found.');
                $response['data'] = [];
            }
            

        } catch (NoSuchEntityException $e) {
            $response['message'] = __('Invalid quote ID.');
        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $response['message'] = __('Something went wrong while fetching coupons.');
        }

        return $this->response->setBody(json_encode($response))->sendResponse();
    }

    /**
     * Get the IDs of applied cart price rules.
     *
     * @return array
     */
    public function getAppliedRules($quote)
    {
        return $quote->getAppliedRuleIds() ? explode(',', $quote->getAppliedRuleIds()) : [];
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
