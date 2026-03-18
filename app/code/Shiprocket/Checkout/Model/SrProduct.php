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

use Magento\Catalog\Model\ProductRepository;
use Shiprocket\Checkout\Logger\SrLogger;

class SrProduct
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param ProductRepository $productRepository
     * @param SrLogger $srLogger
     */
    public function __construct(
        ProductRepository $productRepository,
        SrLogger $srLogger
    ) {
        $this->productRepository = $productRepository;
        $this->srLogger = $srLogger;
    }

    /**
     * Get product data
     *
     * @param string $sku
     * @return object
     */
    public function getProductData($sku)
    {
        return $this->productRepository->get($sku, false, null, true);
    }
}
