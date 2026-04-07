<?php
namespace Codezspark\CouponList\Api;

interface CouponManagementInterface
{
    /**
     * Get list of applicable coupons for a given quote ID
     *
     * @param int $quote_id
     * @return mixed
     */
    public function getApplicableCouponCodes($quote_id);
}
