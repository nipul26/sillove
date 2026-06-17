<?php
namespace Sillove\MobileOtp\Model;

use Sillove\MobileOtp\Helper\Data as ConfigHelper;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

class Msg91Api
{
    protected $configHelper;
    protected $curlFactory;
    protected $logger;

    const SEND_OTP_URL = 'https://control.msg91.com/api/v5/otp';
    const VERIFY_OTP_URL = 'https://control.msg91.com/api/v5/otp/verify';

    public function __construct(
        ConfigHelper $configHelper,
        CurlFactory $curlFactory,
        LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
    }

    /**
     * Send OTP to a mobile number via MSG91 API
     */
    public function sendOtp($mobileNumber)
    {
        if (!$this->configHelper->isEnabled()) {
            return ['type' => 'error', 'message' => 'Mobile OTP Service is currently disabled.'];
        }

        $authKey = $this->configHelper->getAuthKey();
        $templateId = $this->configHelper->getTemplateId();

        // MSG91 v5 API expects the mobile to be passed in parameters, e.g. 919999999999
        $formattedMobile = '91' . ltrim($mobileNumber, ' 0+');

        $url = self::SEND_OTP_URL;
        $params = [
            'template_id' => $templateId,
            'mobile' => $formattedMobile
        ];

        $curl = $this->curlFactory->create();
        $curl->setHeaders([
            'authkey' => $authKey,
            'Content-Type' => 'application/json'
        ]);

        try {
            $curl->post($url, json_encode($params));
            $response = $curl->getBody();
            $result = json_decode($response, true);
            if (isset($result['type']) && $result['type'] === 'error') {
                $this->logger->error('MSG91 Send OTP API Error: ' . json_encode($result));
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('MSG91 Send OTP Request Exception: ' . $e->getMessage());
            return ['type' => 'error', 'message' => 'Unable to connect to OTP service.'];
        }
    }

    /**
     * Verify OTP passed by the user
     */
    public function verifyOtp($mobileNumber, $otp)
    {
        if (!$this->configHelper->isEnabled()) {
            return ['type' => 'error', 'message' => 'Mobile OTP Service is currently disabled.'];
        }

        $authKey = $this->configHelper->getAuthKey();
        $formattedMobile = '91' . ltrim($mobileNumber, ' 0+');

        $url = self::VERIFY_OTP_URL . '?' . http_build_query([
            'otp' => $otp,
            'mobile' => $formattedMobile,
        ]);

        $curl = $this->curlFactory->create();
        $curl->setHeaders([
            'authkey' => $authKey
        ]);

        try {
            $curl->get($url);
            $response = $curl->getBody();
            $result = json_decode($response, true);

            if (isset($result['type']) && $result['type'] === 'error') {
                $this->logger->error('MSG91 Verify OTP API Error: ' . json_encode($result));
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('MSG91 Verify OTP Request Exception: ' . $e->getMessage());
            return ['type' => 'error', 'message' => 'Unable to verify OTP at this time.'];
        }
    }
}
