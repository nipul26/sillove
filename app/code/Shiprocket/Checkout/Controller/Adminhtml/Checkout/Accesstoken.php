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
namespace Shiprocket\Checkout\Controller\Adminhtml\Checkout;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\OauthService;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\Oauth\Token;
use Magento\Framework\App\RequestInterface;
use Shiprocket\Checkout\Helper\Data as ShiprocketHelper;
use Shiprocket\Checkout\Logger\SrLogger;

class Accesstoken extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var IntegrationFactory
     */
    protected $integrationFactory;

    /**
     * @var OauthService
     */
    protected $oauthService;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * @var ShiprocketHelper
     */
    protected $shiprocketHelper;

    /**
     * @var SrLogger
     */
    protected $srLogger;

    /**
     * @param Context               $context
     * @param PageFactory           $resultPageFactory
     * @param Data                  $jsonHelper
     * @param StoreManagerInterface $storeManager
     * @param IntegrationFactory    $integrationFactory
     * @param OauthService          $oauthService
     * @param AuthorizationService  $authorizationService
     * @param Token                 $token
     * @param RequestInterface      $requestInterface
     * @param ShiprocketHelper      $shiprocketHelper
     * @param SrLogger              $srLogger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        StoreManagerInterface $storeManager,
        IntegrationFactory $integrationFactory,
        OauthService $oauthService,
        AuthorizationService $authorizationService,
        Token $token,
        RequestInterface $requestInterface,
        ShiprocketHelper $shiprocketHelper,
        SrLogger $srLogger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->integrationFactory = $integrationFactory;
        $this->oauthService = $oauthService;
        $this->authorizationService = $authorizationService;
        $this->shiprocketHelper = $shiprocketHelper;
        $this->requestInterface = $requestInterface;
        $this->token = $token;
        $this->srLogger = $srLogger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $integrationName = $this->shiprocketHelper->getIntegrationName();
    
            $response = [
                'status' => true,
                'msg' => '',
                'token' => ''
            ];
    
            try {
                $integration = $this->integrationFactory->create()->load($integrationName, 'name');
    
                if (empty($integration->getData())) {
                    $integrationData = [
                        'name' => $integrationName,
                        'email' => '',
                        'status' => '1',
                        'endpoint' => '',
                        'setup_type' => '0'
                    ];
                    // Code to create Integration
                    $integrationFactory = $this->integrationFactory->create();
                    $integration = $integrationFactory->setData($integrationData);
                    $integration->save();
                    $integrationId = $integration->getId();
            
                    // Code to create consumer
                    $consumer = $this->oauthService->createConsumer(['name' => $integrationName]);
                    $consumerId = $consumer->getId();
                    $integration->setConsumerId($consumer->getId());
                    $integration->save();
                    // Code to grant permission
                    $this->authorizationService->grantAllPermissions($integrationId);
            
                    // Code to Activate and Authorize
                    $uri = $this->token->createVerifierToken($consumerId);
                    $this->token->setType('access');
                    $this->token->save();
                    $accessToken = $this->token->getToken();
                
                } else {
                    if ($integration->getStatus()) {
                        $integration->setStatus(1)->save();
                    }
                    $consumer = $this->oauthService->getAccessToken($integration->getConsumerId());
                    $accessToken = $consumer->getToken();
                    $accessSecret = $consumer->getSecret();
                }
                $response['token'] = $accessToken;
                $response['secret'] = $accessSecret;
                $response['apiurl'] = $this->storeManager->getStore()->getBaseUrl().'rest/V1';
            } catch (Exception $e) {
                $this->srLogger->critical($e->getMessage());
                $response['msg'] = $e->getMessage();
                $response['status'] = false;
            }
    
            return $this->jsonResponse($response);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @param array $response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
