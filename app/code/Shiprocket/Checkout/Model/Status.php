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

use \Magento\Framework\Option\ArrayInterface;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var STATUS_ALL values
     */
    protected const STATUS_ALL = '';

    /**
     * @var STATUS_SUCCESS values
     */
    protected const STATUS_SUCCESS = 1;

    /**
     * @var STATUS_FAILED values
     */
    protected const STATUS_FAILED = 0;

    /**
     * Get status option array
     */
    public function toOptionArray()
    {
        return [
            self::STATUS_ALL => __('All'),
            self::STATUS_SUCCESS => __('Success'),
            self::STATUS_FAILED => __('Failed')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::toOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::toOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : '';
    }
}
