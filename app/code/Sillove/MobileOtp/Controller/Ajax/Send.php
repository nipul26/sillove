<?php
namespace Sillove\MobileOtp\Controller\Ajax;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Sillove\MobileOtp\Model\Msg91Api;
use Sillove\MobileOtp\Helper\Data as Helper;

class Send implements HttpPostActionInterface
{
    protected $request;
    protected $resultJsonFactory;
    protected $msg91Api;
    protected $helper;

    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        Msg91Api $msg91Api,
        Helper $helper
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->msg91Api = $msg91Api;
        $this->helper = $helper;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->helper->isEnabled()) {
            return $result->setData([
                'success' => false,
                'message' => __('Mobile OTP login is not available.')
            ]);
        }

        $mobileNumber = $this->request->getParam('mobile_number');

        if (!$mobileNumber) {
            return $result->setData([
                'success' => false,
                'message' => __('Please provide a valid mobile number.')
            ]);
        }

        $apiResponse = $this->msg91Api->sendOtp($mobileNumber);

        if (isset($apiResponse['type']) && $apiResponse['type'] === 'success') {
            return $result->setData([
                'success' => true,
                'message' => __('OTP sent successfully.')
            ]);
        }

        return $result->setData([
            'success' => false,
            'message' => $apiResponse['message'] ?? __('Failed to send OTP.')
        ]);
    }
}
