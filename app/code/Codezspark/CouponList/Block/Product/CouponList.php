<?php

namespace Codezspark\CouponList\Block\Product;

use Codezspark\CouponList\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CouponList extends Template
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
     * @var Registry
     */
    protected $registry;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var int
     */
    protected $initialDisplayCount = 2;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param CollectionFactory $ruleCollectionFactory
     * @param Registry $registry
     * @param TimezoneInterface $timeZone
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        CollectionFactory $ruleCollectionFactory,
        Registry $registry,
        TimezoneInterface $timeZone,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->registry = $registry;
        $this->timeZone = $timeZone;
    }

    /**
     * @return Data
     */
    public function getListHelper()
    {
        return $this->helperData;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return array
     */
    public function getCouponList()
    {
        $rules = $this->ruleCollectionFactory->create()
            ->addCustomerGroupFilter($this->helperData->getGroupId())
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon_type', ['neq' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON])
            ->setOrder('sort_order', 'ASC');

        $currentDate = $this->getCurrentDate();
        $websiteId = $this->helperData->getWebsiteId();
        $ruleArray = [];

        foreach ($rules as $rule) {
            if (!$rule->getCode()
                || !$rule->getShowInCouponList()
                || !in_array($websiteId, $rule->getWebsiteIds())
            ) {
                continue;
            }

            $fromDate = $rule->getFromDate();
            $toDate = $rule->getToDate();
            if (($fromDate != '' && $fromDate > $currentDate)
                || ($toDate != '' && $toDate < $currentDate)
            ) {
                continue;
            }

            $ruleArray[] = [
                'rule_id' => $rule->getId(),
                'name' => $rule->getName(),
                'description' => $rule->getDescription(),
                'coupon' => $rule->getCode(),
            ];
        }

        return $ruleArray;
    }

    /**
     * @return int
     */
    public function getInitialDisplayCount()
    {
        return $this->initialDisplayCount;
    }

    /**
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->timeZone->date()->format('Y-m-d');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->helperData->getModuleStatusConfig()
            && (bool) $this->helperData->getPdpCouponListConfig();
    }
}
