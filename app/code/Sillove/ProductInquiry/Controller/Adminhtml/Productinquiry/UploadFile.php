<?php

namespace Sillove\ProductInquiry\Controller\Adminhtml\ProductInquiry;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Sillove\ProductInquiry\Model\FileUploader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;

class UploadFile extends Action
{
    /**
     * @var FileUploader
     */
    public $fileUploader;

    /**
     * UploadFile Constructor
     *
     * @param Context $context
     * @param FileUploader $fileUploader
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->fileUploader->saveFileToTmpDir('attechment_file');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
