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
namespace Shiprocket\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\OauthService;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Manager;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Shiprocket\Checkout\Logger\SrLogger;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var IntegrationFactory
     */
    protected $integrationFactory;

    /**
     * @var OauthService
     */
    protected $oauthService;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var PhpCookieManager
     */
    protected $cookieManager;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param Context               $context
     * @param ScopeConfigInterface  $scopeConfig
     * @param checkoutSession       $checkoutSession
     * @param IntegrationFactory    $integrationFactory
     * @param OauthService          $oauthService
     * @param Curl                  $curlClient
     * @param ResourceConnection    $resourceConnection
     * @param Manager               $moduleManager
     * @param ProductRepository     $productRepository
     * @param PhpCookieManager      $cookieManager
     * @param SrLogger              $srLogger
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        checkoutSession $checkoutSession,
        IntegrationFactory $integrationFactory,
        OauthService $oauthService,
        Curl $curlClient,
        ResourceConnection $resourceConnection,
        Manager $moduleManager,
        ProductRepository $productRepository,
        PhpCookieManager $cookieManager,
        SrLogger $srLogger
    ) {
        $this->context = $context;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->integrationFactory = $integrationFactory;
        $this->oauthService = $oauthService;
        $this->curlClient = $curlClient;
        $this->resourceConnection = $resourceConnection;
        $this->moduleManager = $moduleManager;
        $this->productRepository = $productRepository;
        $this->cookieManager = $cookieManager;
        $this->srLogger = $srLogger;
        
        parent::__construct($context);
    }

    /**
     * Plugin status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Plugin mode
     *
     * @return boolean
     */
    public function isProduction()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/mode',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Create invoice mmode
     *
     * @return boolean
     */
    public function createInvoice()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/create_invoice',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if discounts need to apply on order items
     *
     * @return boolean
     */
    public function discountsOnProduct()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/product_discount',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Create order log mode
     *
     * @return boolean
     */
    public function createOrderLog()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/ordr_log',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Create debug log
     *
     * @return boolean
     */
    public function createDebugLog()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/general/debug_log',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get order log cleanup frequency
     *
     * @return int
     */
    public function getOrderLogCleanupFrequency()
    {
        $frequency = $this->scopeConfig->getValue(
            'shiprocket_checkout/general/ordr_log_cleanup_frequency',
            ScopeInterface::SCOPE_STORE
        );
        if ($frequency) {
            return $frequency;
        }

        return false;
    }
    
    /**
     * Enabled for product page or not
     *
     * @return boolean
     */
    public function isEnabledOnProductPage()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/productpage/product',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enabled for cart page or not
     *
     * @return boolean
     */
    public function isEnabledOnCartPage()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/cartpage/cart',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enabled for mini cart page or not
     *
     * @return boolean
     */
    public function isEnabledOnMiniCartPage()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/minicartpage/minicart',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Product page integration type
     *
     * @return string
     */
    public function getProductIntegrationType()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/productpage/product_integration_type',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Product page button id
     *
     * @return string
     */
    public function getProductIntegrationIds()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/productpage/product_button_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Product page button text
     *
     * @return string
     */
    public function getProductButtonText()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/productpage/producttxt',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Product page css
     *
     * @return string
     */
    public function getProductPageCSS()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/productpage/productcss',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Cart integration type
     *
     * @return string
     */
    public function getCartIntegrationType()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/cartpage/cart_integration_type',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Cart page button id
     *
     * @return string
     */
    public function getCartIntegrationId()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/cartpage/cart_button_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Cart page button text
     *
     * @return string
     */
    public function getCartButtonText()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/cartpage/carttxt',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Cart page css
     *
     * @return string
     */
    public function getCartPageCSS()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/cartpage/cartcss',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Mini cart button text
     *
     * @return string
     */
    public function getMinicartButtonText()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/minicartpage/minicarttxt',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Mini cart integration type
     *
     * @return string
     */
    public function getMiniCartIntegrationType()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/minicartpage/minicart_type',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Mini cart page button id
     *
     * @return string
     */
    public function getMiniCartIntegrationId()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/minicartpage/minicart_button_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Mini cart css
     *
     * @return string
     */
    public function getMiniCartPageCSS()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/button/minicartpage/minicartcss',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get cod order status
     *
     * @return string
     */
    public function getCodOrderStatus()
    {
        return $this->scopeConfig->getValue(
            'payment/srcheckout_cod/order_status',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get prepaid order status
     *
     * @return string
     */
    public function getPrepaidOrderStatus()
    {
        return $this->scopeConfig->getValue(
            'payment/srcheckout_prepaid/order_status',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default address setting
     *
     * @return boolean
     */
    public function getDefaultAddressSetting()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/customer/setdefaultaddress',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Create order as guest or not
     *
     * @return boolean
     */
    public function getOrderAsGuest()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/customer/guest',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get custom attribute for creating account
     *
     * @return string
     */
    public function getCustomerAttribute()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/customer/customerattribute',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get mobile number prefix
     *
     * @return string
     */
    public function getMobilePrefix()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/customer/mobileprefix',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get checkout session
     *
     * @return checkoutSession
     */
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * Get UPI image url
     *
     * @return string
     */
    public function getUpiOptionImageUrl()
    {
        return 'https://fastrr-boost-ui.pickrr.com/assets/images/boost_button/upi_options.svg';
    }

    /**
     * Get right arrow image url
     *
     * @return string
     */
    public function getRightArrowImageUrl()
    {
        return 'https://fastrr-boost-ui.pickrr.com/assets/images/boost_button/right_arrow.svg';
    }

    /**
     * Get powered by image url
     *
     * @return string
     */
    public function getPoweredImageUrl()
    {
        return 'https://fastrr-boost-ui.pickrr.com/assets/images/boost_button/powered_by.svg';
    }

    /**
     * Get integration name
     *
     * @return string
     */
    public function getIntegrationName()
    {
        return 'ShiprocketCheckout';
    }

    /**
     * Get shiprocket checkout js
     *
     * @return string
     */
    public function getCheckoutJs()
    {
        if ($this->isProduction()) {
            return 'https://fastrr-boost-ui.pickrr.com/assets/js/channels/magento.js';
        }
        return 'https://fastrr-boost-ui-dev.pickrr.com/assets/js/channels/magento.js';
    }

    /**
     * Get shioprocket checkout css
     *
     * @return string
     */
    public function getCheckoutCss()
    {
        if ($this->isProduction()) {
            return 'https://fastrr-boost-ui.pickrr.com/assets/styles/shopify.css';
        }
        return 'https://fastrr-boost-ui-dev.pickrr.com/assets/styles/shopify.css';
    }

    /**
     * Get promise status
     *
     * @return string
     */
    public function isPromiseEnabled()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/promise/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get promise layout custom css
     *
     * @return string
     */
    public function getPromiseCSS()
    {
        return $this->scopeConfig->getValue(
            'shiprocket_checkout/promise/promisecss',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get promise js
     *
     * @return string
     */
    public function getPromiseJs()
    {
        if ($this->isProduction()) {
            return 'https://react-integration.netlify.app/index.js';
        }
        return 'https://react-integration.netlify.app/index.js';
    }

    /**
     * Gte CoD label
     *
     * @return string
     */
    public function getCodLabel()
    {
        return 'COD Charge';
    }

    /**
     * Get Cod code
     *
     * @return string
     */
    public function getCodCode()
    {
        return 'fastrrcodcharge';
    }

    /**
     * Get webhook token
     *
     * @return string
     */
    public function getWebhookToken()
    {
        $response = [];
        
        $integrationName = $this->getIntegrationName();
        $integration = $this->integrationFactory->create()->load($integrationName, 'name');
        $consumer = $this->oauthService->getAccessToken($integration->getConsumerId());
        
        if (!empty($integration->getData())) {
            $response['token'] = $consumer->getToken();
            $response['secret'] = $consumer->getSecret();
        }

        return $response;
    }

    /**
     * Get shiprocket checkout product api end point
     *
     * @return string
     */
    public function getSrProductApiEndPoint()
    {
        if ($this->isProduction()) {
            return 'http://webhookreceiver.pickrr.com/wh/v1/magento/product';
        }
        return 'http://webhookreceiver-dev.pickrr.com/wh/v1/magento/product';
    }

    /**
     * Get shiprocket checkout category api end point
     *
     * @return string
     */
    public function getSrCategoryApiEndPoint()
    {
        if ($this->isProduction()) {
            return 'http://webhookreceiver.pickrr.com/wh/v1/magento/collection';
        }
        return 'http://webhookreceiver-dev.pickrr.com/wh/v1/magento/collection';
    }

    /**
     * Sync data
     *
     * @param array $data
     * @param string $action
     * @return void
     */
    public function sync($data, $action)
    {
        if ($action != '' && $data != '') {
            $jsonData = json_encode($data);
            $token = $this->getWebhookToken();
            if (!isset($token['token']) || !isset($token['secret'])) {
                $this->srLogger->info($action. " Error: Token or Secret key missing!");
                return false;
            }
            $accessToken = $token['token'];
            $accessSecret = $token['secret'];
    
            $hash = hash_hmac('sha256', $jsonData, $accessSecret, true);
            $hmac = base64_encode($hash);
            // $this->srLogger->info('$hmac: '. $hmac);
            if (str_contains($action, 'collections')) {
                $apiUrl = $this->getSrCategoryApiEndPoint()."?access-token=$accessToken";
            } else {
                $apiUrl = $this->getSrProductApiEndPoint()."?access-token=$accessToken";
            }
    
            // $this->srLogger->info('data: '. $jsonData);
            try {
                $header = [
                    'X-Magento-Hmac-Sha256' => $hmac,
                    'X-Magento-Topic' => $action,
                    'Content-Type' =>  'application/json'
                ];
                $this->curlClient->setHeaders($header);
                $this->curlClient->post($apiUrl, $jsonData);
                $responseBody = $this->curlClient->getBody();
                $response = json_decode($responseBody, true);
                if ($this->curlClient->getStatus() != 200) {
                    $this->srLogger->info("cUrl Error: ".$responseBody);
                    return false;
                }
                // $this->srLogger->info($responseBody);
                return true;
            } catch (Exception $e) {
                $this->srLogger->info($action. " Error: ". $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Get product salable quantity
     *
     * @param string $sku
     * @return int
     */
    public function getProductAvailableQty($sku)
    {
        try {
            $connection = $this->resourceConnection->getConnection();

            $cpeTableName = $this->resourceConnection->getTableName('catalog_product_entity');
            $isiTableName = $this->resourceConnection->getTableName('inventory_source_item');
            $irTableName = $this->resourceConnection->getTableName('inventory_reservation');

            if ($this->moduleManager->isEnabled('Magento_Inventory')) {
                $select = $connection->select()
                ->from(['cpe'=>$cpeTableName], ['sku'=>'cpe.sku'])
                ->join(['isi'=>$isiTableName], "isi.sku=cpe.sku", ['qty'=>'isi.quantity'])
                ->joinLeft(['ir'=>$irTableName], "ir.sku=cpe.sku", ['reserved_qty'=> "COALESCE(SUM(ir.quantity), 0)"])
                ->where('cpe.sku = ?', $sku)
                ->group('cpe.sku');
                $result = $connection->fetchRow($select);
                if (is_array($result) && !empty($result)) {
                    return (int) ($result['qty'] + $result['reserved_qty']);
                }
            } else {
                $product = $this->productRepository->get($sku, false, null, true);
                $stock = $product->getExtensionAttributes()->getStockItem();
                return $stock->getQty();
            }
        } catch (Exception $e) {
            $this->srLogger->info('Error: '.$e->getMessage());
        }

        return 0;
    }

    /**
     * Get utm source from cookie
     *
     * @param string $cookieName
     * @return string
     */
    public function getUtmSource($cookieName = '')
    {
        if ($cookieName) {
            return $this->cookieManager->getCookie($cookieName);
        } else {
            $cookieValue =  $this->cookieManager->getCookie('sr_utm_data');
            if ($cookieValue) {
                $cookieValue =  str_replace("%", "", $cookieValue);
                $utmSourceStr = urldecode($cookieValue);
                if ($utmSourceStr) {
                    $utmArray = $this->getValidUtmSource($utmSourceStr);
                    if (!empty($utmArray)) {
                        return http_build_query($utmArray, '', '&');
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get valid utm source
     *
     * @param string $utmSourceStr
     * @return array
     */
    public function getValidUtmSource($utmSourceStr)
    {
        $utmArray = [];
        $utmSourceArr = explode('&', $utmSourceStr);
        if (!empty($utmSourceArr)) {
            foreach ($utmSourceArr as $utmSource) {
                $utmArrayTemp = explode('=', $utmSource);
                if (!empty($utmArrayTemp)) {
                    $utmArray[$utmArrayTemp[0]] = $utmArrayTemp[1];
                }
            }
        }
        if (!isset($utmArray['utm_source'])) {
            if (isset($utmArray['gbraid']) || isset($utmArray['gclid'])) {
                $utmArray['utm_source'] = 'google';
            }
            if (isset($utmArray['fbclid'])) {
                $utmArray['utm_source'] = 'facebook';
            }
        }

        return $utmArray;
    }

    /**
     * Get utm source from cookie as array
     *
     * @return array
     */
    public function getUtmSourceArray()
    {
        $utmSourceStr = $this->getUtmSource();
        $utmArray = [];
        if ($utmSourceStr) {
            $utmSourceArr = explode('&', $utmSourceStr);
            if (!empty($utmSourceArr)) {
                foreach ($utmSourceArr as $utmSource) {
                    $utmArrayTemp = explode('=', $utmSource);
                    if (!empty($utmArrayTemp)) {
                        $utmArray[$utmArrayTemp[0]] = $utmArrayTemp[1];
                    }
                }
            }
        }

        return $utmArray;
    }
}
